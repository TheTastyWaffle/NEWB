<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Techno
 *
 * @ORM\Table(name="techno")
 * @ORM\Entity
 */
class Techno
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="idtechno", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtechno;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Techno
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
     * Get idtechno
     *
     * @return integer
     */
    public function getIdtechno()
    {
        return $this->idtechno;
    }
}
