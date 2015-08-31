<?php

namespace AppBundle\Entity;

class Teaching
{
    public $id;
    public $schoolyear;
    public $teacher;
    public $class;
    public $subject;
    public function __construct($row)
    {
        $this->id = $row["ts_id"];
        $this->schoolyear = $row["ts_sy_id"];
        $this->teacher = $row["ts_tea_id"];
        $this->class = $row["ts_cl_id"];
        $this->subject = $row["ts_sub_id"];
    }

    public static function GetFull($row)
    {
        $t=new Teaching($row);
        $t->schoolyear=new SchoolYear($row);
        $t->teacher=new Teacher($row);
        $t->class=new Klass($row);
        $t->subject=new Subject($row);
        return $t;
    }
}



