<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TechUser
 *
 * @ORM\Table(name="tech_user", indexes={@ORM\Index(name="idtechno_idx", columns={"idtechno"}), @ORM\Index(name="iduser_idx", columns={"iduser"})})
 * @ORM\Entity
 */
class TechUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idtech_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtechUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduser", referencedColumnName="iduser")
     * })
     */
    private $iduser;

    /**
     * @var \AppBundle\Entity\Techno
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Techno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtechno", referencedColumnName="idtechno")
     * })
     */
    private $idtechno;



    /**
     * Get idtechUser
     *
     * @return integer
     */
    public function getIdtechUser()
    {
        return $this->idtechUser;
    }

    /**
     * Set iduser
     *
     * @param \AppBundle\Entity\User $iduser
     *
     * @return TechUser
     */
    public function setIduser(\AppBundle\Entity\User $iduser = null)
    {
        $this->iduser = $iduser;

        return $this;
    }

    /**
     * Get iduser
     *
     * @return \AppBundle\Entity\User
     */
    public function getIduser()
    {
        return $this->iduser;
    }

    /**
     * Set idtechno
     *
     * @param \AppBundle\Entity\Techno $idtechno
     *
     * @return TechUser
     */
    public function setIdtechno(\AppBundle\Entity\Techno $idtechno = null)
    {
        $this->idtechno = $idtechno;

        return $this;
    }

    /**
     * Get idtechno
     *
     * @return \AppBundle\Entity\Techno
     */
    public function getIdtechno()
    {
        return $this->idtechno;
    }
}
