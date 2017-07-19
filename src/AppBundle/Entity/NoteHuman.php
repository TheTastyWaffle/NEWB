<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteHuman
 *
 * @ORM\Table(name="note_human", indexes={@ORM\Index(name="note_human_user_iduser_fk", columns={"iduser"}), @ORM\Index(name="note_human_user_iduser_fk2", columns={"idsender"})})
 * @ORM\Entity
 */
class NoteHuman
{
    /**
     * @var integer
     *
     * @ORM\Column(name="note", type="integer", nullable=true)
     */
    private $note;

    /**
     * @var integer
     *
     * @ORM\Column(name="idnote_human", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idnoteHuman;

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
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduser", referencedColumnName="iduser")
     * })
     */
    private $iduser;



    /**
     * Set note
     *
     * @param integer $note
     *
     * @return NoteHuman
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
     * Get idnoteHuman
     *
     * @return integer
     */
    public function getIdnoteHuman()
    {
        return $this->idnoteHuman;
    }

    /**
     * Set idsender
     *
     * @param \AppBundle\Entity\User $idsender
     *
     * @return NoteHuman
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

    /**
     * Set iduser
     *
     * @param \AppBundle\Entity\User $iduser
     *
     * @return NoteHuman
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
}
