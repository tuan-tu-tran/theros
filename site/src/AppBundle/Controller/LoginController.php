<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Teacher;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Route("/", name="home")
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
                $t->getRoles($db);
                $this->session()->set("user", $t);
                if ($t->passwordChanged) {
                    return $this->redirectToRoute(HomeController::GetHomeRoute($t));
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
     * The page that is redirected to in case a user tries to access an unauthorized page
     * @Route("/not-authorized", name="not_authorized")
     */
    public function notAuthorizedAction()
    {
        return $this->render("login/not_authorized.html.twig");
    }
}
