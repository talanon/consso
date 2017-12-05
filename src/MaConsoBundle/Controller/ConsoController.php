<?php

namespace MaConsoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ClientType;


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
            //If the client has completed the formula before, redirect to the info page
        } else {
            $client_name = $formService->checkClientIntegrity($session->get('name'));
            if ($formService->checkClientIntegrity($client_name) == null)
                return $this->redirectToRoute('tableau');
            //If the client  hasn't completed the formula before, or the client's name doesn't exist
            else {
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
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            //$client->setCompleted(true);
            $formService->saveClient($client);
            return $this->redirectToRoute('tableau');
        }
        return $this->render('MaConsoBundle::form.html.twig',
            array(
                'name' => $client->getName(),
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/tableau-de-bord/{code}",name="tableau")
     * Display the information page
     */
    public function tableauAction(Request $request, $code = null)
    {
        $formService = $this->get('formService');
        $consoService = $this->get('consoService');
        $session = $this->get('session');
        //Verify the user existence and info integrity
        if (!$session->has('name')) {
            $client_name = $formService->createClient()->getName();
            $session->set('name', $client_name);
            return $this->redirectToRoute('questionnaire');
            //If the client hasn't completed the formula before or the user doesn't exist, redirect to the form page
        } else {
            $client_name = $formService->checkClientIntegrity($session->get('name'));

            if ($formService->checkClientIntegrity($client_name) != null) {
                $session->set('name', $client_name);
                return $this->redirectToRoute('questionnaire');
            } //In this case the client exists and has completed the formula before
            else {
                $client = $formService->findClientByName($client_name);
                $consomation = $consoService->estimateConso($client);
                $advices = $consoService->generateAdvice($client);
                return $this->render('MaConsoBundle::tableau.html.twig',
                    array(
                        'client' => $client,
                        'advices' => $advices,
                        'consommation' => $consomation,
                        'companies' => $formService->findCompanies()
                    ));
            }
        }
    }

    /**
     * @Route("/configuration", name="configuration")
     */
    public function tableauEditAction(Request $request, $code = null)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $formService = $this->get('formService');
        $session = $this->get('session');
        $predictable_room = ['Cuisine', 'Salle de bain', 'Chambre parentale', 'Chambre enfant', 'Salon', 'Bureau'];

        //?
        if ($session->get('name')) {
            $client = $repository_client->findOneBy(array('name' => $session->get('name')));
        } elseif ($code != null) {
            $client = $repository_client->findOneBy(array('name' => $code));
        }

        //If the client's info is imcolpleted, redirect to the form
        if (!isset($client) || !$client->getPiece()) {
            return $this->redirectToRoute('questionnaire');
        }

        //Remove an object in the room
        if (isset($_POST['obj']) && $_POST['obj'] != '') {
            $formService->removeObject();
        }

        //When the user submit one room, save or update the room and objects
        if (isset($_POST['submit']) && $_POST['name'] != '') {
            //FormService: saveOrUpdateRoom($room_name, $client_id, $object_names, $object_quantities, $object_powers, $object_uses)
            $formService->saveOrUpdateRoom($_POST['name'], $client->getId(), $_POST['names'], $_POST['quantities'], $_POST['powers'], $_POST['uses']);
        }

        $rooms = $repository_room->findBy(array('clientId' => $client->getId()));
        if ($rooms == null) {
            for ($i = 1; $i <= $client->getPiece(); $i++) {
                $room = new Room();
                $room->setClientId($client->getId());
                if ($i <= count($predictable_room)) {
                    $room->setName($predictable_room[$i - 1]);
                } else {
                    $room->setName("Autre" + $i);
                }
                $em->persist($room);
                $em->flush();
            }

            // TODO fermer la session

            $rooms = $repository_room->findBy(array('clientId' => $client->getId()));
            foreach ($rooms as $room) {
                switch ($room->getName()) {
                    case "Cuisine":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        $this->addObj($room->getId(), 'Réfrigirateur', 1, 250, 24);
                        $this->addObj($room->getId(), 'Lave vaisselle', 1, 1500, 0.5);
                        $this->addObj($room->getId(), 'Hotte', 1, 150, 0.02);
                        $this->addObj($room->getId(), 'Microndes', 1, 800, 0.08);
                        $this->addObj($room->getId(), 'Machine à café', 1, 1000, 0.15);
                        $this->addObj($room->getId(), 'Grille pain', 1, 100, 0.01);
                        if ($client->getPlaque()) {
                            $this->addObj($room->getId(), 'Plaques électriques', 1, 2000, 1);
                        }

                        if ($client->getFour()) {
                            $this->addObj($room->getId(), 'Four', 1, 2500, 0.20);
                        }

                        break;
                    case "Salle de bain":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        $this->addObj($room->getId(), 'Lave linge', 1, 2200, 0.75);
                        if ($client->getLinge()) {
                            $this->addObj($room->getId(), 'Sèche linge', 1, 3000, 0.75);
                        }
                        $this->addObj($room->getId(), 'Sèche cheveux', 1, 600, 0.20);
                        //$this->addObj($room->getId(),'Chauffe eau',1,80,1);
                        break;
                    case "Chambre parentale":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        $this->addObj($room->getId(), 'Lampe de chevet', 2, 60, 1);
                        $this->addObj($room->getId(), 'Radio reveil', 1, 10, 24);
                        break;
                    case "Chambre enfant":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        $this->addObj($room->getId(), 'Lampe de chevet', 1, 60, 1);
                        $this->addObj($room->getId(), 'Radio reveil', 1, 10, 24);
                        break;
                    case "Salon":
                        $this->addObj($room->getId(), 'Ampoule', 2, 60, 2);
                        $this->addObj($room->getId(), 'Télévision', 1, 200, 4);
                        $this->addObj($room->getId(), 'Sono', 1, 80, 4);
                        $this->addObj($room->getId(), 'Box internet', 1, 5, 24);
                        break;
                    case "Bureau":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        $this->addObj($room->getId(), 'Lampe de chevet', 1, 60, 1);
                        $this->addObj($room->getId(), 'Ordinateur', 1, 80, 4);
                        break;
                    case "Autre":
                        if ($client->getAmpoule()) {
                            $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                        } else {
                            $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                        }
                        break;
                }

            }
        }
        $i = 0;
        foreach ($rooms as $room) {
            $objects[$i] = $repository_object->findBy(array('roomId' => $room->getId()));
            $i++;
        }

        $rep_company = $this->getDoctrine()->getRepository("AppBundle:Company");
        $companies = $rep_company->findAll();
        if ($client->getChauffage()) {
            $chauffage = $client->getSurface() * 11.7;
            $surface = $client->getSurface();
        } else {
            $chauffage = 0;
            $surface = 0;
        }


        return $this->render('MaConsoBundle::configuration.html.twig',
            array(
                'name' => $client->getName(),
                'rooms' => $rooms,
                'objects' => $objects,
                'companies' => $companies,
                'chauffage' => $chauffage,
                'surface' => $surface,
                //'form' => $form->createView(),
            ));
    }

}