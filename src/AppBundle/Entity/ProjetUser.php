<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjetUser
 *
 * @ORM\Table(name="projet_user", indexes={@ORM\Index(name="projet_user_user_iduser_fk", columns={"iduser"}), @ORM\Index(name="FK_FA41396634A83105", columns={"idprojet"})})
 * @ORM\Entity
 */
class ProjetUser
{
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=true)
     */
    private $state = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="idprojet_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idprojetUser;

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
     * @var \AppBundle\Entity\Projet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Projet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idprojet", referencedColumnName="idprojet")
     * })
     */
    private $idprojet;



    /**
     * Set text
     *
     * @param string $text
     *
     * @return ProjetUser
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ProjetUser
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return ProjetUser
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get idprojetUser
     *
     * @return integer
     */
    public function getIdprojetUser()
    {
        return $this->idprojetUser;
    }

    /**
     * Set iduser
     *
     * @param \AppBundle\Entity\User $iduser
     *
     * @return ProjetUser
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
     * Set idprojet
     *
     * @param \AppBundle\Entity\Projet $idprojet
     *
     * @return ProjetUser
     */
    public function setIdprojet(\AppBundle\Entity\Projet $idprojet = null)
    {
        $this->idprojet = $idprojet;

        return $this;
    }

    /**
     * Get idprojet
     *
     * @return \AppBundle\Entity\Projet
     */
    public function getIdprojet()
    {
        return $this->idprojet;
    }
}
