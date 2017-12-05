<?php
/**
 * Created by PhpStorm.
 * User: dc
 * Date: 2017/12/5
 * Time: 9:57
 */

namespace MaConsoBundle\Service;

use Doctrine\ORM\EntityManager;
class ConsoService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    //TODO calculer la consommation d'un utilisateur
    public function calculateConso($client)
    {
        return 37;
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