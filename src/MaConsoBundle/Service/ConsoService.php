<?php
/**
 * Created by PhpStorm.
 * User: dc
 * Date: 2017/12/5
 * Time: 9:57
 */

namespace MaConsoBundle\Service;

use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Entity\Room;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
class ConsoService
{
    protected $em;
    protected $repository_room;
    protected $repository_object;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository_room = $this->em->getRepository("AppBundle:Room");
        $this->repository_object = $this->em->getRepository("AppBundle:Object");
    }

    public function computeObject(Client $client){
        $rooms = $this->repository_room->findBy(array('clientId' => $client->getId()));
        $sum=0;
        foreach ($rooms as $room) {
            /**
             * @var $room Room
             */
            $objects = $this->repository_object->findBy(array('roomId' => $room->getId()));
            /**
             * @var $object Object
             */
            foreach ($objects as $object){
                $object->getId();
                $sum += $object->getUtilisation()*$object->getPower()*$object->getQuantity();
            }
        }

        return round(($sum/1000)*0.1593*30.5);
    }


    //TODO calculer la consommation d'un utilisateur
    public function calculateConso(Client $client)
    {
        $calcul = $this->computeObject($client);
        return $calcul;
    }

    //TODO creer les conseils et les implémenter dans advice
    public function generateAdvices($client)
    {
        return [
            0 => 'Fermez les volets pour conserver la chaleur',
            1 => 'Utilisez un thermosat intelligent'
        ];
    }

}