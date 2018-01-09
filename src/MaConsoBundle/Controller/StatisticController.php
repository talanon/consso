<?php
/**
 * Created by PhpStorm.
 * User: dc
 * Date: 2017/11/13
 * Time: 22:40
 */

namespace MaConsoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $surface_appartement = $statisticService->calculateAverage('surface', $clients_appartement);
        $surface_maison = $statisticService->calculateAverage('surface', $clients_maison);

        $foyer_appartement = $statisticService->calculateAverage('foyer', $clients_appartement);
        $foyer_maison = $statisticService->calculateAverage('foyer', $clients_maison);

        $piece_appartement = $statisticService->calculateAverage('piece', $clients_appartement);
        $piece_maison = $statisticService->calculateAverage('piece', $clients_maison);

        $eco_appartement = $statisticService->calculateAverage('eco', $clients_appartement);
        $eco_maison = $statisticService->calculateAverage('eco', $clients_maison);

        return $this->render('MaConsoBundle::statistic.html.twig', array(
                'client_number' => $client_number,
                'client_appartement_number' => $clients_appartement_number,
                'client_maison_number' => $clients_maison_number,
                'surface_appartement' => $surface_appartement,
                'surface_maison' => $surface_maison,
                'foyer_appartement' => $foyer_appartement,
                'foyer_maison' => $foyer_maison,
                'piece_appartement' => $piece_appartement,
                'piece_maison' => $piece_maison,
                'eco_appartement' => $eco_appartement * 100,
                'eco_maison' => $eco_maison * 100
            )
        );
    }

    /**
     * @Route("/statistic/getData", name="getStatData")
     */
    public function getData()
    {
        $type_logement = $_POST['type_logement'];
        $type = $_POST['type'];
        $nombre = $_POST['nombre'];
        $statisticService = $this->get('statisticService');
        if (strcasecmp($type, "surface") == 0) {
            //Get surface range
            $min = floor($nombre / 10) * 10;
            $max = $min + 10;
            $clients = $statisticService->findClientsByTypeAndSurface($type_logement, $min, $max);
        } else {
            $clients = $statisticService->findClientsByType($type_logement,$type,$nombre);
        }
        $client_number = count($clients);
        $objectsArray = $statisticService->findObjectsByClients($clients);
        $objectQuantity = $statisticService->getObjectsQuantityStat($objectsArray, $client_number);
        $electricityUsed = $statisticService->getElectricityStat($objectsArray, $client_number);
        $return = array("object_quantity" => $objectQuantity,
            "electricity_used" => $electricityUsed);
        return new JsonResponse($return);
    }


}