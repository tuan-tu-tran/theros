<?php

namespace AppBundle\Entity;

class Subject
{
    public $id;
    public $code;
    public $description;
    public function __construct($row)
    {
        $this->id = $row["sub_id"];
        $this->code = $row["sub_code"];
        $this->description = $row["sub_desc"];
    }
}


