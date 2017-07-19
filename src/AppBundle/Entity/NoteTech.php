<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteTech
 *
 * @ORM\Table(name="note_tech", indexes={@ORM\Index(name="idsender_idx", columns={"idsender"}), @ORM\Index(name="idtech_user_idx", columns={"idtech_user"})})
 * @ORM\Entity
 */
class NoteTech
{
    /**
     * @var integer
     *
     * @ORM\Column(name="note", type="integer", nullable=false)
     */
    private $note;

    /**
     * @var integer
     *
     * @ORM\Column(name="idnote_tech", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idnoteTech;

    /**
     * @var \AppBundle\Entity\TechUser
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TechUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtech_user", referencedColumnName="idtech_user")
     * })
     */
    private $idtechUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsender", referencedColumnName="iduser")
     * })
     */
    private $idsender;



    /**
     * Set note
     *
     * @param integer $note
     *
     * @return NoteTech
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Get idnoteTech
     *
     * @return integer
     */
    public function getIdnoteTech()
    {
        return $this->idnoteTech;
    }

    /**
     * Set idtechUser
     *
     * @param \AppBundle\Entity\TechUser $idtechUser
     *
     * @return NoteTech
     */
    public function setIdtechUser(\AppBundle\Entity\TechUser $idtechUser = null)
    {
        $this->idtechUser = $idtechUser;

        return $this;
    }

    /**
     * Get idtechUser
     *
     * @return \AppBundle\Entity\TechUser
     */
    public function getIdtechUser()
    {
        return $this->idtechUser;
    }

    /**
     * Set idsender
     *
     * @param \AppBundle\Entity\User $idsender
     *
     * @return NoteTech
     */
    public function setIdsender(\AppBundle\Entity\User $idsender = null)
    {
        $this->idsender = $idsender;

        return $this;
    }

    /**
     * Get idsender
     *
     * @return \AppBundle\Entity\User
     */
    public function getIdsender()
    {
        return $this->idsender;
    }
}
