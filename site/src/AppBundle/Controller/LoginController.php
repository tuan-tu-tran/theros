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
                $this->session()->set("user", $t);
                if ($t->passwordChanged) {
                    return $this->redirectToRoute("homepage");
                } else {
                    return $this->redirectToRoute("init_password");
                }
            }
        } else {
            $this->session()->set("user", NULL);
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
        $user=$this->user();
        if (!$user) {
            return $this->redirectToRoute("login");
        } else if ($user->passwordChanged) {
            return $this->redirectToRoute("homepage");
        }

        $request = $this->request();
        if ($request->getMethod() == "POST") {
            $password = $request->request->get("password");
            if (!$password) {
                throw new \Exception("no password provided ".var_export($password, TRUE));
            }
            $user->initPassword($this->db(), $password);
            return $this->redirectToRoute("homepage");
        } else {
            $this->info("%d %d %d", (bool)"0", "" == NULL, "0" == NULL);
            return $this->render("login/init_password.html.twig");
        }

    }
}
