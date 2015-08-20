<?php

namespace AppBundle\Entity;

class Klass
{
    public $id;
    public $code;
    public function __construct($row)
    {
        $this->id = $row["cl_id"];
        $this->code = $row["cl_desc"];
    }
}

