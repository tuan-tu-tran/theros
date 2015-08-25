<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Teacher;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction()
    {
        $db=$this->db();
        $teachers=Teacher::GetAll($db);
        return $this->render("login/index.html.twig", array(
            "teachers"=>$teachers
        ));
    }
}
