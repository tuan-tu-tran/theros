<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Driver\Connection as Db;

class Work
{
    public $id;
    public $type;
    public $student;
    public $subject;
    public $schoolyear;
    public $teacher;
    public $description;
    public $result;
    public $hasResult;

    public function __construct($row)
    {
        $this->id = $row["w_id"];
        $this->type= $row["w_type"] == 1?"TDV":"RAN";
        $this->student = $row["w_st_id"];
        $this->subject = $row["w_sub_id"];
        $this->schoolyear = $row["w_sy_id"];
        $this->teacher = $row["w_tea_id"];
        $this->description = $row["w_description"];
        $this->result = $row["w_result"];
        $this->hasResult = (bool)$row["w_has_result"];
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

    /**
     * Return an full instance of work whose properties are objects
     * by its id.
     * The student->class property is also set.
     */
    public static function GetFullById(Db $db, $id)
    {
        $s=$db->prepare("
            SELECT *
            FROM work
            JOIN schoolyear ON sy_id = w_sy_id
            JOIN student ON st_id = w_st_id
            JOIN student_class ON sc_st_id = st_id AND sc_sy_id = sy_id
            JOIN class ON cl_id = sc_cl_id
            JOIN subject ON sub_id = w_sub_id
            LEFT JOIN teacher ON tea_id = w_tea_id
            WHERE w_id = :id
        ");
        $s->bindValue("id", $id, \PDO::PARAM_INT);
        $s->execute();
        $row=$s->fetch();
        if($row){
            $w=self::GetFull($row);
            $w->student->class=new Klass($row);
            return $w;
        } else {
            return NULL;
        }
    }
}
