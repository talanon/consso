<?php

namespace MaConsoBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Entity\Room;

Class FormService
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

    public function createClient()
    {
        $mot = array('MLH', 'BAL', 'FAC', 'BIS', 'CAC', 'DPS');
        do {
            $passphrase = $mot[random_int(0, 5)] . random_int(1, 1000);
        } while ($client = $this->repository_client->findBy(
            array('name' => '"' . $passphrase . '"')
        ));
        $client = new Client();
        $client->setName($passphrase);
        $this->saveClient($client);
        return $client;
    }

    public function saveClient($client)
    {
        $this->em->persist($client);
        $this->em->flush();
    }

    public function findCompanies()
    {
        return $this->repository_company->findAll();
    }

    public function findClientByName($client_name)
    {
        return $this->repository_client->findOneBy(array('name' => $client_name));
    }

    public function checkClient($client_name)
    {
        $client = $this->repository_client->findOneBy(array('name' => $client_name));
        if ($client) {
            return null;
        } else {
            return $this->createClient()->getName();
        }
    }

    private function addObj($id, $name, $quantity, $power, $utilisation)
    {
        $object = new Object();
        $object->setRoomId($id);
        $object->setName($name);
        $object->setQuantity($quantity);
        $object->setPower($power);
        $object->setUtilisation($utilisation);
        $this->em->persist($object);
        $this->em->flush();
    }

    public function findRoom($client_id, $room_name)
    {
        return $this->repository_room->findOneBy(array('clientId' => $client_id, 'name' => $room_name));
    }

    public function findRooms($client)
    {
        return $this->repository_room->findBy(array('clientId' => $client->getId()));
    }
    public function saveOrUpdateRoom($room_name, $client_id, $object_names, $object_quantities, $object_powers, $object_uses)
    {
        $room = $this->repository_room->findOneBy(array('clientId' => $client_id, 'name' => $room_name));
        if (!$room) {
            $room = new Room();
            $room->setClientId($client_id);
            $room->setName($room_name);
            $this->em->persist($room);
            $this->em->flush();
        }
        foreach ($object_names as $name) {
            $this->addObj($room->getId(), $name, $object_quantities[$k], $object_powers[$k], $object_uses[$k]);
        }
        return $room;
    }

    public function initiateRooms($client){
        $predictable_room = ['Cuisine', 'Salle de bain', 'Chambre parentale', 'Chambre enfant', 'Salon', 'Bureau'];
        //Save the rooms for the client
        for ($i = 1; $i <= $client->getPiece(); $i++) {
            $room = new Room();
            $room->setClientId($client->getId());
            if ($i <= count($predictable_room)) {
                $room->setName($predictable_room[$i - 1]);
            } else {
                $room->setName("Autre" + $i);
            }
            $this->em->persist($room);
            $this->em->flush();
        }
        $rooms = $this->repository_room->findBy(array('clientId' => $client->getId()));
        //Save objects in each room
        foreach ($rooms as $room) {
            switch ($room->getName()) {
                case "Cuisine":
                    if ($client->getAmpoule()) {
                        $this->addObj($room->getId(), 'Ampoule', 1, 20, 5);
                    } else {
                        $this->addObj($room->getId(), 'Ampoule', 1, 60, 5);
                    }
                    $this->addObj($room->getId(), 'Réfrigirateur', 1, 200, 24);
                    $this->addObj($room->getId(), 'Lave vaisselle', 1, 625, 0.5);
                    $this->addObj($room->getId(), 'Hotte', 1, 150, 0.02);
                    $this->addObj($room->getId(), 'Microndes', 1, 800, 0.08);
                    $this->addObj($room->getId(), 'Machine à café', 1, 1000, 0.15);
                    $this->addObj($room->getId(), 'Grille pain', 1, 100, 0.01);
                    if ($client->getPlaque()) {
                        $this->addObj($room->getId(), 'Plaques électriques', 1, 2000, 0.5);
                    }

                    if ($client->getFour()) {
                        $this->addObj($room->getId(), 'Four', 1, 2000, (1.5/7));
                    }

                    break;
                case "Salle de bain":
                    if ($client->getAmpoule()) {
                        $this->addObj($room->getId(), 'Ampoule', 1, 10, 4);
                    } else {
                        $this->addObj($room->getId(), 'Ampoule', 1, 60, 4);
                    }
                    $this->addObj($room->getId(), 'Lave linge', 1, 2200, 4/7);
                    if ($client->getLinge()) {
                        $this->addObj($room->getId(), 'Sèche linge', 1, 2500, 2/7);
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
        return $rooms;
    }

    public function findObjectsInRooms($rooms){
        $i = 0;
        foreach ($rooms as $room) {
            $objects[$i] = $this->repository_object->findBy(array('roomId' => $room->getId()));
            $i++;
        }
        return $objects;
    }

    public function removeObj($object_id){
        $obj = $this->repository_object->findOneById($object_id);
        $this->em->remove($obj);
        $this->em->flush();
    }
}