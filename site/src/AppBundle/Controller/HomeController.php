<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Teacher;

class HomeController
{
    public static function GetHomeRoute(Teacher $user)
    {
        if ($user->isAdmin()) {
            return "admin_home";
        } else {
            return "teacher_home";
        }
    }
}
