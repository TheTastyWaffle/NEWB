<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="comment___fk1", columns={"idsender"}), @ORM\Index(name="comment_user_iduser_fk", columns={"idreciever"})})
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true)
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="idcomment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcomment;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idreciever", referencedColumnName="iduser")
     * })
     */
    private $idreciever;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Comment
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
     * Set text
     *
     * @param string $text
     *
     * @return Comment
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
     * Get idcomment
     *
     * @return integer
     */
    public function getIdcomment()
    {
        return $this->idcomment;
    }

    /**
     * Set idreciever
     *
     * @param \AppBundle\Entity\User $idreciever
     *
     * @return Comment
     */
    public function setIdreciever(\AppBundle\Entity\User $idreciever = null)
    {
        $this->idreciever = $idreciever;

        return $this;
    }

    /**
     * Get idreciever
     *
     * @return \AppBundle\Entity\User
     */
    public function getIdreciever()
    {
        return $this->idreciever;
    }

    /**
     * Set idsender
     *
     * @param \AppBundle\Entity\User $idsender
     *
     * @return Comment
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
