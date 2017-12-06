<?php
/**
 * Created by PhpStorm.
 * User: dc
 * Date: 2017/12/5
 * Time: 23:06
 */

namespace MaConsoBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Entity\Room;

Class StatisticService
{
    protected $em;
    protected $repository_client;
    protected $repository_company;
    protected $repository_room;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository_client = $this->em->getRepository("AppBundle:Client");
        $this->repository_company = $this->em->getRepository("AppBundle:Company");
        $this->repository_room = $this->em->getRepository("AppBundle:Room");
        $this->repository_object = $this->em->getRepository("AppBundle:Object");
    }

    public function findClients()
    {
        return $this->repository_client->findAll();
    }

    public function findClientsByLogement($logement_type)
    {
        return $this->repository_client->findBy(array('logement' => $logement_type));
    }

    public function calculateAverage($attribute,$clients)
    {
        $sum = 0;
        $count = 0;
        $client = new Client();
        foreach($clients as $client){
            switch($attribute){
                case "surface":
                    $sum += $client->getSurface();
                    $count++;
                    break;
            }
        }
        return number_format($sum/$count,2);
    }
}