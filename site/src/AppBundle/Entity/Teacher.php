<?php

namespace AppBundle\Entity;

class Teacher
{
    public $id;
    public $fullname;
    public function __construct($row)
    {
        $this->id = $row["tea_id"];
        $this->fullname = $row["tea_fullname"];
    }
}




