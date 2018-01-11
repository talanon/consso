<?php

namespace MaConsoBundle\Controller;


use AppBundle\Entity\Gestes;
use AppBundle\Entity\Room;
use AppBundle\Repository\GestesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ClientType;
use AppBundle\Form\AddObjType;
use AppBundle\Form\AddRoomType;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Repository\RoomRepository;


Class GestesController extends Controller
{
    /**
     * @Route("/les-bons-gestes", name="gestes")
     * Action before the user start the form
     */
    public function gestesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gestes_repository = $em->getRepository(Gestes::class);
        $array = [];
        $tips = $gestes_repository->findAll();
        foreach ($tips as $tip){
            if(!in_array($tip->getTag(),$array)){
                array_push($array,$tip->getTag());
            }
        }

        return $this->render('MaConsoBundle::tips.html.twig',
            array(
                'tips' => $tips,
                'categories' => $array,
                'index' => ''
                )
        );
    }

    /**
     * @Route("/les-bons-gestes/{str}", name="gestes-a")
     * Action before the user start the form
     */
    public function conseilAction(Request $request, $str)
    {
        $em = $this->getDoctrine()->getManager();
        $gestes_repository = $em->getRepository(Gestes::class);
        $array = [];
        $gestes = $gestes_repository->findAll();
        $tips = $gestes_repository->findByTag($str);
        foreach ($gestes as $geste){
            if(!in_array($geste->getTag(),$array)){
                array_push($array,$geste->getTag());
            }
        }

        return $this->render('MaConsoBundle::tips.html.twig',
            array(
                'tips' => $tips,
                'categories' => $array,
                'index' => $str
            )
        );
    }
}