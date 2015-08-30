<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Driver\Connection as Db;

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

    /**
     * Return a list of Subject instances taught by a teacher in a class during a schoolyear
     */
    public static function GetByTeacherAndClass(Db $db, $schoolyear, $teacherId, $classId)
    {
        $s=$db->prepare("
            SELECT sub.*
            FROM teacher_subject
            JOIN schoolyear ON sy_id = ts_sy_id
            JOIN subject sub ON sub_id = ts_sub_id
            WHERE ts_tea_id = :teacherId
            AND sy_desc = :schoolyear
            AND ts_cl_id = :classId
            ORDER BY sub_code
        ");
        $s->bindValue("teacherId", $teacherId, \PDO::PARAM_INT);
        $s->bindValue("classId", $classId, \PDO::PARAM_INT);
        $s->bindValue("schoolyear", $schoolyear, \PDO::PARAM_STR);
        $s->execute();
        $result = $s->fetchAll();
        $subjects=[];
        foreach ($result as $row) {
            $subjects[] = new Subject($row);
        }
        return $subjects;
    }

}


