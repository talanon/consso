<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * prout
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\proutRepository")
 */
class Client
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $logement;

    /**
     * @ORM\Column(type="integer", length=100, nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chauffage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $surface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $foyer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $piece;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $completed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ampoule;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $linge;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $four;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $plaque;
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Client
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return Client
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set logement
     *
     * @param string $logement
     *
     * @return Client
     */
    public function setLogement($logement)
    {
        $this->logement = $logement;

        return $this;
    }

    /**
     * Get logement
     *
     * @return string
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set chauffage
     *
     * @param boolean $chauffage
     *
     * @return Client
     */
    public function setChauffage($chauffage)
    {
        $this->chauffage = $chauffage;

        return $this;
    }

    /**
     * Get chauffage
     *
     * @return boolean
     */
    public function getChauffage()
    {
        return $this->chauffage;
    }

    /**
     * Set surface
     *
     * @param integer $surface
     *
     * @return Client
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;

        return $this;
    }

    /**
     * Get surface
     *
     * @return integer
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * Set foyer
     *
     * @param integer $foyer
     *
     * @return Client
     */
    public function setFoyer($foyer)
    {
        $this->foyer = $foyer;

        return $this;
    }

    /**
     * Get foyer
     *
     * @return integer
     */
    public function getFoyer()
    {
        return $this->foyer;
    }

    /**
     * Set piece
     *
     * @param integer $piece
     *
     * @return Client
     */
    public function setPiece($piece)
    {
        $this->piece = $piece;

        return $this;
    }

    /**
     * Get piece
     *
     * @return integer
     */
    public function getPiece()
    {
        return $this->piece;
    }

    /**
     * Set completed
     *
     * @param boolean $completed
     *
     * @return Client
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Client
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set ampoule
     *
     * @param boolean $ampoule
     *
     * @return Client
     */
    public function setAmpoule($ampoule)
    {
        $this->ampoule = $ampoule;

        return $this;
    }

    /**
     * Get ampoule
     *
     * @return boolean
     */
    public function getAmpoule()
    {
        return $this->ampoule;
    }

    /**
     * Set linge
     *
     * @param boolean $linge
     *
     * @return Client
     */
    public function setLinge($linge)
    {
        $this->linge = $linge;

        return $this;
    }

    /**
     * Get linge
     *
     * @return boolean
     */
    public function getLinge()
    {
        return $this->linge;
    }

    /**
     * Set four
     *
     * @param boolean $four
     *
     * @return Client
     */
    public function setFour($four)
    {
        $this->four = $four;

        return $this;
    }

    /**
     * Get four
     *
     * @return boolean
     */
    public function getFour()
    {
        return $this->four;
    }

    /**
     * Set plaque
     *
     * @param boolean $plaque
     *
     * @return Client
     */
    public function setPlaque($plaque)
    {
        $this->plaque = $plaque;

        return $this;
    }

    /**
     * Get plaque
     *
     * @return boolean
     */
    public function getPlaque()
    {
        return $this->plaque;
    }
}
