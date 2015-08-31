<?php

namespace AppBundle\Entity;

class Student
{
    public $id;
    public $name;
    public function __construct($row)
    {
        $this->id = $row["st_id"];
        $this->name = $row["st_name"];
    }
}

