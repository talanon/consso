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
        $statisticService = $this->get('statisticService');

        $clients_appartement = $statisticService->findClientsByLogement('appartement');
        $clients_maison = $statisticService->findClientsByLogement('maison');

        //Client number
        $clients_appartement_number = count($clients_appartement);
        $clients_maison_number = count($clients_maison);
        $client_number = $clients_appartement_number + $clients_maison_number;

        //Calculate average surface
        $surface_appartement = $statisticService->calculateAverage('surface',$clients_appartement);
        $surface_maison = $statisticService->calculateAverage('surface',$clients_maison);

        return $this->render('MaConsoBundle::statistic.html.twig',array(
                'client_number'=>$client_number,
                'client_appartement_number'=>$clients_appartement_number,
                'client_maison_number'=>$clients_maison_number,
                'surface_appartement'=>$surface_appartement,
                'surface_maison'=>$surface_maison
            )
        );
    }


}