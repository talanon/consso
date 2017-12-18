<?php

namespace MaConsoBundle\Controller;


use AppBundle\Entity\Gestes;
use AppBundle\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ClientType;
use AppBundle\Form\AddObjType;
use AppBundle\Form\AddRoomType;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Repository\RoomRepository;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

Class ConsoController extends Controller
{

    /**
     * @Route("/maconso", name="maconso")
     * Action before the user start the form
     */
    public function indexAction(Request $request)
    {
        $formService = $this->get('formService');
        $session = $this->get('session');
        $client_name = null;
        //If no client name is restored in the session, a new client will be created and its name will be restored in the session
        if (!$session->has('name')) {
            $client_name = $formService->createClient()->getName();
            $session->set('name', $client_name);
            //If the client exists, redirect to the info page
        } else {
            $client_name = $formService->checkClient($session->get('name'));
            if ($client_name == null) {
                return $this->redirectToRoute('tableau');
                //If the client's name doesn't exist
            } else {
                $session->set('name', $client_name);
            }
        }
        return $this->render('MaConsoBundle::beforeStarting.html.twig',
            array(
                'name' => $client_name,
                'companies' => $formService->findCompanies(),
            ));
    }

    /**
     * @Route("/maconso/questionnaire", name="questionnaire")
     * Display the form
     */
    public function formAction(Request $request)
    {
        $formService = $this->get('formService');
        $session = $this->get('session');
        $client = $formService->findClientByName($session->get('name'));
        dump($client);
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $formService->saveClient($client);
            return $this->redirectToRoute('tableau');
        }
        return $this->render('MaConsoBundle::form.html.twig',
            array(
                'name' => $client->getName(),
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/tableau-de-bord/{code}",name="tableau")
     * Display the information page
     */
    public function tableauAction(Request $request, $code = null)
    {
        $formService = $this->get('formService');
        $consoService = $this->get('consoService');
        $em = $this->getDoctrine()->getManager();
        $gestes_repository = $em->getRepository(Gestes::class);
        $tips = $gestes_repository->findAll();

        $session = $this->get('session');

        //If code is indicated in the URL
        if ($code != null) {
            $client = $formService->findClientByName($code);
            //If the client exists
            if($client != null){
                $consomation = $consoService->estimateConso($client);
                $advices = $consoService->generateAdvices($client);
                //Clear user name stored in the session
                $session->remove('name');
                return $this->render('MaConsoBundle::tableau.html.twig',
                    array(
                        'client' => $client,
                        'advices' => $advices,
                        'consommation' => $consomation,
                        'companies' => $formService->findCompanies()
                    )
                );
            }
        }
        //If user name is not restored in the session, return to index
        if (!$session->has('name')) {
            return $this->redirectToRoute('maconso');
        }
        //If the client's name is stored in the session
        $client_name = $formService->checkClient($session->get('name'));
        //If the client exists in the DB
        if ($client_name == null) {
            $client = $formService->findClientByName($session->get('name'));
            $consomation = $consoService->calculateConso($client);

            return $this->render('MaConsoBundle::tableau.html.twig',
                array(
                    'client' => $client,
                    'consommation' => $consomation,
                    'companies' => $formService->findCompanies(),
                    'tips' => $tips
                )
            );
            //If the user name in the session doesn't exist, redirect to the form page
        }else {
            $session->set('name',$client_name);
            return $this->redirectToRoute('questionnaire');
        }

    }

    /**
     * @Route("/configuration", name="configuration")
     */
    public function tableauEditAction(Request $request)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $formService = $this->get('formService');
        $session = $this->get('session');
        $predictable_room = ['Cuisine', 'Salle de bain', 'Chambre parentale', 'Chambre enfant', 'Salon', 'Bureau'];

        //If client's name is not restored in the session
        if (!$session->get('name')) {
            return $this->redirectToRoute('questionnaire');
        }

        $client = $formService->findClientByName($session->get('name'));
        if (!isset($client) || !$client->getPiece($session->get('name'))) {
            return $this->redirectToRoute('questionnaire');
        }


        //Remove an object in the room
        if (isset($_POST['obj']) && $_POST['obj'] != '') {
            $formService->removeObj($_POST['obj']);
        }

        //When the user submit one room, save or update the room and objects
        if (isset($_POST['submit']) && $_POST['name'] != '') {
            //FormService: saveOrUpdateRoom($room_name, $client_id, $object_names, $object_quantities, $object_powers, $object_uses)
            $formService->saveOrUpdateRoom($_POST['name'], $client->getId(), $_POST['names'], $_POST['quantities'], $_POST['powers'], $_POST['uses']);
        }

        $rooms = $formService->findRooms($client);
        if ($rooms == null) {
            $rooms = $formService->initiateRooms($client);
        };

        $objects = $formService->findObjectsInRooms($rooms);

        $companies = $formService->findCompanies();
        if ($client->getChauffage()) {
            $chauffage = $client->getSurface() * 11.7;
            $surface = $client->getSurface();
        } else {
            $chauffage = 0;
            $surface = 0;
        }

        $form_obj = $this->createForm(AddObjType::class, null);
        $form_room = $this->createForm(AddRoomType::class, null);

        return $this->render('MaConsoBundle::configuration.html.twig',
            array(
                'name' => $client->getName(),
                'rooms' => $rooms,
                'objects' => $objects,
                'companies' => $companies,
                'chauffage' => $chauffage,
                'surface' => $surface,
                'form_obj' => $form_obj->createView(),
                'form_room' => $form_room->createView(),
            ));
    }

    /**
     * @Route("/ajax_addObj",name="add_object")
     */
    public function addObjAction(Request $request)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $repository_room = $this->getDoctrine()->getRepository("AppBundle:Room");
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        $client = $repository_client->findOneBy(array('name' => $session->get('name')));

        $type = $request->get('type');

        switch ($type) {
            case 'obj':
                $id = $request->get('id');
                $name = $request->get('name');
                $quantity = $request->get('quantity');
                $utilisation = $request->get('utilisation');
                $power = $request->get('power');

                $obj = new Object();
                $obj->setRoomId($id);
                $obj->setName($name);
                $obj->setQuantity($quantity);
                $obj->setutilisation($utilisation);
                $obj->setPower($power);
                $em->persist($obj);
                $em->flush();
                return new JsonResponse(array('status' => 'TRUE'));
                break;
            case 'room':
                $name = $request->get('name');
                $room = new Room();
                $room->setName($name);
                $room->setClientId($client->getId());
                $em->persist($room);
                $em->flush();
                return new JsonResponse(array('status' => 'TRUE'));
                break;
            case 'delete':
                $room = $request->get('room');
                $droom = $repository_room->findOneBy(array('id' => $room));
                $em->remove($droom);
                $em->flush();
                break;
        }

        return new JsonResponse(array('status' => 'FALSE'));
    }

    /**
     * @Route("/vote/{code}/{response}",name="estimate")
     */
    public function estimateAction(Request $request, $code = null, $response)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        if($session->get('name')) {
            $client = $repository_client->findOneBy(array('name'=>$session->get('name')));
        }
        elseif ($code != null){
            $client = $repository_client->findOneBy(array('name'=>$code));
        }

        if(!isset($client )|| !$client->getPiece()) {
            return $this->redirectToRoute('questionnaire');
        }
        switch ($response){
            case 'true':
                $client->setHasVoted(true);
                break;
            case 'false':
                $client->setHasVoted(false);
                break;
        }
        $em->persist($client);
        $em->flush();
        return $this->redirectToRoute('tableau');
    }

    /**
     * @Route("/solution", name="solution")
     */
    public function solutionAction(Request $request)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $repository_object = $this->getDoctrine()->getRepository("AppBundle:Object");
        $repository_room = $this->getDoctrine()->getRepository("AppBundle:Room");
        $repository_company = $this->getDoctrine()->getRepository("AppBundle:Company");
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        $companies = $repository_company->findAll();
        $client = $repository_client->findOneBy(array('name'=>$session->get('name')));
        $conso = 0;
        $average = 0;
        if($client->getAmpoule()){
            $conso = 0;
        }
        else {
            $rooms = $repository_room->findByClientId($client->getId());
            foreach ($rooms as $room){
                $ampoules = $repository_object->findBy(array("roomId"=>$room->getId(), "name"=>"Ampoule"));
                foreach ($ampoules as $ampoule){
                    $conso += $ampoule->getPower()*$ampoule->getUtilisation();
                    $average += $ampoule->getUtilisation()*10;
                }
            }
            if($conso<$average){
                $conso=0;
            }
        }
        if($client->getChauffage()){
            $chauffage=$client->getSurface()*11.7;
            $surface = $client->getSurface();
        }
        else {
            $chauffage = 0;
            $surface = 0;
        }
        $i=0;
        $rooms = $repository_room->findByClientId($client->getId());
        foreach($rooms as $room){
            $objects[$i] = $repository_object->findBy(array('roomId'=>$room->getId()));
            $i++;
        }
        return $this->render('MaConsoBundle:Default:solution.html.twig',
            array(
                'name'=>$client->getName(),
                'conso'=>$conso,
                'average'=>$average,
                'companies' =>$companies,
                'chauffage' =>$chauffage,
                'surface' =>$surface,
                'rooms' =>$rooms,
                'objects'=>$objects,
                //'form' => $form->createView(),
            ));
    }

}