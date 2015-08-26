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
        $request=$this->request();
        if($request->getMethod() == "POST"){
            $id=$request->request->get("teacherId");
            $password=$request->request->get("password");
            $t=Teacher::GetById($db, $id);
            if (!$t) {
                throw $this->createNotFoundException("no such teacher: $id");
            } elseif ($t->password != md5($password)) {
                $this->flash()->set("bad_password", "1");
                return $this->redirectToRoute("login");
            } else {
                return $this->redirectToRoute("homepage");
            }
        } else {
            $teachers=Teacher::GetAll($db);
            return $this->render("login/index.html.twig", array(
                "teachers"=>$teachers
            ));
        }
    }

    /**
     * Reinitialize the teacher password upon first login
     * or redirect to home page if not the first login
     *
     * @Route("/init-password", name="init_password")
     */
    public function initPasswordAction()
    {
        return $this->render("login/init_password.html.twig");
    }
}
