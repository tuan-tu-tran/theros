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
}




