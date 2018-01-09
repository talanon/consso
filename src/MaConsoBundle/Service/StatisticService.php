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

    public function findClientsByTypeAndSurface($type_logement,$surface_min,$surface_max)
    {
        $query = $this->repository_client->createQueryBuilder('c')
            ->where('c.surface >= :min')
            ->andWhere('c.surface < :max')
            ->setParameters(array('min' => $surface_min, 'max' => $surface_max));
        if(!empty($type_logement))
        {
            $query->andWhere('c.logement = :logement_type')
                  ->setParameter('logement_type',$type_logement);
        }
        $query = $query->getQuery();
        $clients = $query->getResult();
        return $clients;
    }

    public function findClientsByType($type_logement, $type, $nombre)
    {
        $query = $this->repository_client->createQueryBuilder('c');
        if(empty($type_logement))
        {
            if(!empty($type))
            {
                $query->where('c.'.$type.'= :nombre')
                    ->setParameter('nombre',$nombre);
            }
        }else
        {
            $query->where('c.logement = :type_logement')
                  ->setParameter('type_logement',$type_logement);
            if(!empty($type))
            {
                $query->andWhere('c.'.$type.'= :nombre')
                    ->setParameter('nombre',$nombre);
            }
        };
        $query = $query->getQuery();
        $clients = $query->getResult();
        return $clients;
    }

    public function findObjectsByClients($clients)
    {
        $result = array();
        foreach($clients as $client)
        {
            $rooms = $this->findRoomsByClientId($client);
            foreach ($rooms as $room)
            {
                $objects = $this->findObjectsByRoomId($room);
                array_push($result,$objects);
            }
        }
        return $result;
    }

    public function getObjectsQuantityStat($objectsArray,$client_number)
    {
        $result = array('Ampoule'=>0, 'Lampe de chevet'=>0,'Ordinateur'=>0, 'Télévision'=>0, 'Lave linge'=>0
                        ,'Réfrigirateur'=>0, 'Lave vaisselle'=>0,'Plaques électriques'=>0
                        ,'Hotte'=>0, 'Microndes'=>0, 'Machine à café'=>0, 'Grille pain'=>0
                        ,'Sèche linge'=>0, 'Autre'=>0);
        if($client_number == 0)
            return $result;
        foreach($objectsArray as $objects)
        {
            foreach($objects as $object)
            {
                if(array_key_exists($object->getName(), $result))
                {
                    $result[$object->getName()] += $object->getQuantity();
                } else
                {
                    $result['Autre']  += $object->getQuantity();
                }
            }
        }
        foreach ($result as $item=>$item_value)
        {
            $result[$item] = floatval(number_format($item_value/$client_number,1));
        }
        return $result;
    }

    public function getElectricityStat($objectsArray,$client_number)
    {
        $result = array('Ampoule'=>0, 'Lampe de chevet'=>0,'Ordinateur'=>0, 'Télévision'=>0, 'Lave linge'=>0
        ,'Réfrigirateur'=>0, 'Lave vaisselle'=>0,'Plaques électriques'=>0
        ,'Hotte'=>0, 'Microndes'=>0, 'Machine à café'=>0, 'Grille pain'=>0
        ,'Sèche linge'=>0,'Autre'=>0);
        foreach($objectsArray as $objects)
        {
            foreach($objects as $object)
            {
                if(array_key_exists($object->getName(), $result))
                {
                    $result[$object->getName()] += $object->getQuantity()*$object->getUtilisation()*$object->getPower();
                } else
                {
                    $result['Autre']  += $object->getQuantity()*$object->getUtilisation()*$object->getPower();
                }
            }
        }
        foreach ($result as $item=>$item_value)
        {
            $result[$item] = $item_value/$client_number;
        }
        return $result;
    }

    public function calculateAverage($attribute,$clients)
    {
        $sum = 0;
        $count = 0;
        foreach($clients as $client){
            switch($attribute){
                case "surface":
                    $sum += $client->getSurface();
                    $count++;
                    break;
                case "foyer":
                    $sum += $client->getFoyer();
                    $count++;
                    break;
                case "piece":
                    $sum += $client->getPiece();
                    $count++;
                    break;
                case "eco":
                    $sum += $client->getAmpoule();
                    $count++;
                    break;
            }
        }
        if ($attribute == "eco")
            return $count != 0?number_format($sum/$count,3):0;
        return $count != 0?number_format($sum/$count,1):0;
    }

    private function findRoomsByClientId(Client $client)
    {
        return $this->repository_room->findBy(array('clientId' => $client->getId()));
    }

    private function findObjectsByRoomId(Room $room)
    {
        return $this->repository_object->findBy(array('roomId' => $room->getId()));
    }

}