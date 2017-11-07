<?php

namespace MaConsoBundle\Controller;

use AppBundle\Entity\Chinois;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Entity\Room;
use Doctrine\DBAL\Types\IntegerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/maconso", name="maconso")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Client");
        $rep_company = $this->getDoctrine()->getRepository("AppBundle:Company");
        $dachen = new Chinois();

        $em = $this->getDoctrine()->getEntityManager();
        $dachen->setName('Dachen');
        $session = $this->get('session');
        $mot=array('MLH', 'BAL', 'FAC', 'BIS', 'CAC', 'DPS');

        if(!$session->has('name')){

            do {
                $passphrase = $mot[random_int(0,5)].random_int(1,1000);
            }while($client = $repository->findBy(
                array('name' => '"'.$passphrase.'"'== null)
            ));


            $client = new Client();
            $client->setName($passphrase);
            $session->set('name',$passphrase);
            $em->persist($client);
            $em->flush();
        }
        else {
            $client = $repository->findOneBy(array('name'=>$session->get('name')));
            if($client->getCompleted()==true){
                $this->redirectToRoute("tableau");
            }
        }

        $companies = $rep_company->findAll();

        return $this->render('MaConsoBundle:Default:layout.html.twig',
            array(
                'name'=>$client->getName(),
                'companies'=>$companies,
            ));
    }

    /**
     * @Route("/maconso/{number}", name="question")
     */
    public function formAction(Request $request, $number)
    {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Client");
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');

        $client = $repository->findOneBy(array('name'=>$session->get('name')));

        if($number==1){
            $title = 'Avant tout ...';
            $form = $this->createFormBuilder($client)
                ->add('chauffage', ChoiceType::class, array('label'=>'Chauffez-vous votre logement avec des radiateurs électriques ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
                ->add('save', SubmitType::class, array('label' => 'suivant'))
                ->getForm();
        }

        if($number==2){
            $title = 'A propos de votre logement...';
            if($client->getChauffage()==true){
                $form = $this->createFormBuilder($client)
                    ->add('logement', ChoiceType::class, array('label'=>'Votre logement est ', 'choices' => array("Un appartement"=>"appartement", "Une maison"=>"maison"),'required' => true,'data' => null))
                    ->add('age', ChoiceType::class, array('choices' => array("- de 10 ans"=>0, "10 à 15 ans"=>10, "16 à 30 ans"=>16, "+ de 30 ans"=>30),'required' => true,'data' => null))
                    ->add('surface', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, array('label'=>'Votre surface ?','required' => true,'data' => null))
                    ->add('save', SubmitType::class, array('label' => 'suivant'))
                    ->getForm();
            }
            else {
                $form = $this->createFormBuilder($client)
                    ->add('logement', ChoiceType::class, array('label'=>'Votre logement est ', 'choices' => array("Un appartement"=>"appartement", "Une maison"=>"maison"),'required' => true,'data' => null))
                    ->add('save', SubmitType::class, array('label' => 'suivant'))
                    ->getForm();
            }

        }

        if($number==3){
            $title = 'A propos de votre équipement...';
            $form = $this->createFormBuilder($client)
                ->add('ampoule', ChoiceType::class, array('label'=>'Avez-vous des ampoules basses consommation ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
                ->add('four', ChoiceType::class, array('label'=>'Avez-vous un four éléctrique ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
                ->add('plaque', ChoiceType::class, array('label'=>'Avez-vous un des plaques éléctriques ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
                ->add('linge', ChoiceType::class, array('label'=>'Avez-vous un sèche linge ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
                ->add('save', SubmitType::class, array('label' => 'suivant'))
                ->getForm();
        }

        if($number==4){
            $title = 'A propos de votre foyer...';
                $form = $this->createFormBuilder($client)
                    ->add('foyer',  \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, array('label'=>'De combien de personnes est composé votre foyer ?','required' => true,'data' => null))
                    ->add('piece',  \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, array('label'=>'De combien de pièces est composé votre foyer ?','required' => true,'data' => null))
                    ->add('save', SubmitType::class, array('label' => 'suivant'))
                    ->getForm();
        }

        $form->handleRequest($request);
        $fin=4;

        if ($form->isSubmitted() && $form->isValid()) {

            $client = $form->getData();
            if($number==$fin){
                $client->setCompleted(true);
            }
            $em->persist($client);
            $em->flush();
            if($number==$fin){
                return $this->redirectToRoute('tableau');
            }
            return $this->redirectToRoute('question', array('number'=>$number+1));
        }

        return $this->render('MaConsoBundle:Default:form.html.twig',
            array(
                'name'=>$client->getName(),
                'form' => $form->createView(),
                'title' =>$title,
                'number' => $number,
            ));
    }

    public function addObj($id,$name,$quantity,$power,$utilisation){
        $em = $this->getDoctrine()->getEntityManager();

        $object = new Object();
        $object->setRoomId($id);
        $object->setName($name);
        $object->setQuantity($quantity);
        $object->setPower($power);
        $object->setUtilisation($utilisation);
        $em->persist($object);
        $em->flush();
    }

    /**
     * @Route("/tableaudebord", name="tableau")
     */
    public function tableauAction(Request $request)
    {
        $repository_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $repository_room = $this->getDoctrine()->getRepository("AppBundle:Room");
        $repository_object = $this->getDoctrine()->getRepository("AppBundle:Object");
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');

        $predictable_room = ['Cuisine', 'Salle de bain', 'Chambre parentale', 'Chambre enfant','Salon', 'Bureau'];

        $client = $repository_client->findOneBy(array('name'=>$session->get('name')));

        if(isset($_POST['obj']) && $_POST['obj']!=''){
            $obj = $repository_object->findOneById($_POST['obj']);
            $em->remove($obj);
            $em->flush();
        }

        if(isset($_POST['submit']) && $_POST['name']!=''){
            $room = $repository_room->findOneBy(array('clientId'=>$client->getId(), 'name'=>$_POST['name']));;
            if($room){
                $k=0;
                foreach($_POST['names'] as $name){
                    $this->addObj($room->getId(),$name,$_POST['quantities'][$k],$_POST['powers'][$k],$_POST['uses'][$k]);
                    $k++;
                }
            }else{
                $room = new Room();
                $room->setClientId($client->getId());
                $room->setName($_POST['name']);
                $em->persist($room);
                $em->flush();
                $k=0;
                foreach($_POST['names'] as $name){
                    $this->addObj($room->getId(),$name,$_POST['quantities'][$k],$_POST['powers'][$k],$_POST['uses'][$k]);
                    $k++;
                }
            }


        }

        $rooms = $repository_room->findBy(array('clientId'=>$client->getId()));
        if($rooms==null){
            for($i=1;$i<=$client->getPiece();$i++){
                $room = new Room();
                $room->setClientId($client->getId());
                if($i<=count($predictable_room)){$room->setName($predictable_room[$i-1]);}
                else{$room->setName("Autre"+$i);}
                $em->persist($room);
                $em->flush();
            }
            $rooms = $repository_room->findBy(array('clientId'=>$client->getId()));
            foreach($rooms as $room){
                switch ($room->getName()){
                    case "Cuisine":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        $this->addObj($room->getId(),'Réfrigirateur',1,250,24);
                        $this->addObj($room->getId(),'Lave vaisselle',1,1500,0.5);
                        $this->addObj($room->getId(),'Hotte',1,150,0.02);
                        $this->addObj($room->getId(),'Microndes',1,800,0.08);
                        $this->addObj($room->getId(),'Machine à café',1,1000,0.15);
                        $this->addObj($room->getId(),'Grille pain',1,100,0.01);
                        if($client->getPlaque()){
                            $this->addObj($room->getId(),'Plaques électriques',1,2000,1);
                        }

                        if($client->getFour()){
                            $this->addObj($room->getId(),'Four',1,2500,0.20);
                        }

                        break;
                    case "Salle de bain":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        $this->addObj($room->getId(),'Lave linge',1,2200,0.75);
                        if($client->getLinge()){
                            $this->addObj($room->getId(),'Sèche linge',1,3000,0.75);
                        }
                        $this->addObj($room->getId(),'Sèche cheveux',1,600,0.20);
                        //$this->addObj($room->getId(),'Chauffe eau',1,80,1);
                        break;
                    case "Chambre parentale":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        $this->addObj($room->getId(),'Lampe de chevet',2,60,1);
                        $this->addObj($room->getId(),'Radio reveil',1,10,24);
                        break;
                    case "Chambre enfant":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        $this->addObj($room->getId(),'Lampe de chevet',1,60,1);
                        $this->addObj($room->getId(),'Radio reveil',1,10,24);
                        break;
                    case "Salon":
                        $this->addObj($room->getId(),'Ampoule',2,60,2);
                        $this->addObj($room->getId(),'Télévision',1,200,4);
                        $this->addObj($room->getId(),'Sono',1,80,4);
                        $this->addObj($room->getId(),'Box internet',1,5,24);
                        break;
                    case "Bureau":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        $this->addObj($room->getId(),'Lampe de chevet',1,60,1);
                        $this->addObj($room->getId(),'Ordinateur',1,80,4);
                        break;
                    case "Autre":
                        if($client->getAmpoule()){
                            $this->addObj($room->getId(),'Ampoule',1,10,4);
                        }
                        else {
                            $this->addObj($room->getId(),'Ampoule',1,60,4);
                        }
                        break;
                }

            }
        }
        $i=0;
        foreach($rooms as $room){
            $objects[$i] = $repository_object->findBy(array('roomId'=>$room->getId()));
            $i++;
        }

        $rep_company = $this->getDoctrine()->getRepository("AppBundle:Company");
        $companies = $rep_company->findAll();
        if($client->getChauffage()){
            $chauffage=$client->getSurface()*11.7;
            $surface = $client->getSurface();
        }
        else {
            $chauffage = 0;
            $surface = 0;
        }


        return $this->render('MaConsoBundle:Default:tableau.html.twig',
            array(
                'name'=>$client->getName(),
                'rooms'=>$rooms,
                'objects'=>$objects,
                'companies' =>$companies,
                'chauffage' =>$chauffage,
                'surface' => $surface,
                //'form' => $form->createView(),
            ));
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
