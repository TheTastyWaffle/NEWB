<?php
namespace AppBundle\Entity;

class ProjectForm
{
    protected $name;
    protected $description;
    protected $begin;
    protected $end;

    /**
     * @return mixed
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param mixed $begin
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
    }



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }



}