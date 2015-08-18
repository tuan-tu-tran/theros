<?php

namespace AppBundle\Entity;

class RawData
{
    public $id;
    public $student;
    public $class;
    public $description;
    public $treated;
    public function __construct($row)
    {
        $this->id = $row["rd_id"];
        $this->student = $row["rd_st_id"];
        $this->class = $row["rd_cl_id"];
        $this->treated = $row["rd_treated"];
        $this->description=$row["rd_desc"];
    }
}
