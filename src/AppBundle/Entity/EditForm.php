<?php
namespace AppBundle\Entity;

class EditForm
{
    protected $password;
    protected $confpassword;
    protected $firstname;
    protected $lastname;
    protected $telephone;
    protected $file;
    protected $age;
    protected $availabe;

    /**
     * @return mixed
     */
    public function getAvailabe()
    {
        return $this->availabe;
    }

    /**
     * @param mixed $availabe
     */
    public function setAvailabe($availabe)
    {
        $this->availabe = $availabe;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }



    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getConfpassword()
    {
        return $this->confpassword;
    }

    /**
     * @param mixed $confpassword
     */
    public function setConfpassword($confpassword)
    {
        $this->confpassword = $confpassword;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }



}