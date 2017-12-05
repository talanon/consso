<?php

namespace MaConsoBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;

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

    public function checkClientIntegrity($client_name)
    {
        $client = $this->repository_client->findOneBy(array('name' => $client_name));
        if ($client) {
            if ($client->getCompleted()) {
                return null;
            } else {
                return $client_name;
            }
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
}