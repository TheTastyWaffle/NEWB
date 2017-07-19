<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupeUser
 *
 * @ORM\Table(name="groupe_user", indexes={@ORM\Index(name="groupe_user___fk1", columns={"iduser"}), @ORM\Index(name="groupe_user_groupe_idgroupe_fk", columns={"idgroupe"})})
 * @ORM\Entity
 */
class GroupeUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idgroupe_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idgroupeUser;

    /**
     * @var \AppBundle\Entity\Groupe
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Groupe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idgroupe", referencedColumnName="idgroupe")
     * })
     */
    private $idgroupe;

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
     * Get idgroupeUser
     *
     * @return integer
     */
    public function getIdgroupeUser()
    {
        return $this->idgroupeUser;
    }

    /**
     * Set idgroupe
     *
     * @param \AppBundle\Entity\Groupe $idgroupe
     *
     * @return GroupeUser
     */
    public function setIdgroupe(\AppBundle\Entity\Groupe $idgroupe = null)
    {
        $this->idgroupe = $idgroupe;

        return $this;
    }

    /**
     * Get idgroupe
     *
     * @return \AppBundle\Entity\Groupe
     */
    public function getIdgroupe()
    {
        return $this->idgroupe;
    }

    /**
     * Set iduser
     *
     * @param \AppBundle\Entity\User $iduser
     *
     * @return GroupeUser
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
