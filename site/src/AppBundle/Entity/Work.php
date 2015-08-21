<?php

namespace AppBundle\Entity;

class Work
{
    public $id;
    public $type;
    public $student;
    public $subject;
    public $schoolyear;
    public $teacher;
    public $description;

    public function __construct($row)
    {
        $this->id = $row["w_id"];
        $this->type= $row["w_type"] == 1?"TDV":"RAN";
        $this->student = $row["w_st_id"];
        $this->subject = $row["w_sub_id"];
        $this->schoolyear = $row["w_sy_id"];
        $this->teacher = $row["w_tea_id"];
        $this->description = $row["w_description"];
    }

    public static function GetFull($row)
    {
        $w=new Work($row);
        $w->student = new Student($row);
        $w->subject = new Subject($row);
        $w->schoolyear = new SchoolYear($row);
        $w->teacher = Teacher::FromRow($row);
        return $w;
    }
}
