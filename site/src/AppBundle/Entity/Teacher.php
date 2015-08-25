<?php

namespace AppBundle\Entity;

class Teacher
{
    public $id;
    public $fullname;
    public function __construct($row)
    {
        $this->id = $row["tea_id"];
        $this->fullname = $row["tea_fullname"];
    }

    /**
     * Create a Teacher instance from a db row (assoc array) or null if the tea_id is not found
     */
    public static function FromRow($row)
    {
        if(isset($row["tea_id"]) && $row["tea_id"]){
            return new Teacher($row);
        } else {
            return NULL;
        }
    }

    /**
     * Get a list of all teachers using the given database connection
     */
    public static function GetAll($db)
    {
        $list=[];
        $result=$db->query("
            SELECT *
            FROM teacher
            ORDER BY tea_fullname
        ")->fetchAll();
        foreach($result as $row){
            $list[]=self::FromRow($row);
        }
        return $list;
    }
}




