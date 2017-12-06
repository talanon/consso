<?php
/**
 * Created by PhpStorm.
 * User: dc
 * Date: 2017/11/13
 * Time: 22:40
 */

namespace MaConsoBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StatisticController extends Controller
{
    /**
     * @Route("/statistic", name="statistic")
     */
    public function statisticAction()
    {
        $rep_client = $this->getDoctrine()->getRepository("AppBundle:Client");
        $em = $this->getDoctrine()->getEntityManager();
        $list_client = $rep_client->findAll();
        $client_number = count($list_client);
        return $this->render('MaConsoBundle::statistic.html.twig',array(
                'client_number'=>$client_number
            )
        );
    }


}