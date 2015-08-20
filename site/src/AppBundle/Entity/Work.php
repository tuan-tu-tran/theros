<?php

namespace AppBundle\Entity;

class Work
{
    public $id;
    public $type;
    public $teaching;
    public $student;
    public $description;

    public function __construct($row)
    {
        $this->id = $row["w_id"];
        $this->type= $row["w_type"] == 1?"TDV":"RAN";
        $this->teaching = $row["w_ts_id"];
        $this->student = $row["w_st_id"];
        $this->description = $row["w_description"];
    }

    public static function GetFull($row)
    {
        $w=new Work($row);
        $w->teaching = Teaching::GetFull($row);
        $w->student = new Student($row);
        return $w;
    }
}
