<?php

namespace AppBundle\Entity;

class Student
{
    public $id;
    public $name;
    public $tutor;
    public $address;
    public $zip;
    public $city;
    public function __construct($row)
    {
        $this->id = $row["st_id"];
        $this->name = $row["st_name"];
        $this->tutor = $row["st_tutor"];
        $this->address = $row["st_address"];
        $this->zip = $row["st_zip"];
        $this->city = $row["st_city"];
    }
}

