<?php

namespace AppBundle\Entity;

class SchoolYear
{
    public $id;
    public $description;
    public function __construct($row)
    {
        $this->id = $row["sy_id"];
        $this->description = $row["sy_desc"];
    }
}



