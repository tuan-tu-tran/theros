<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Let teachers see assigned works and encode results and init their password
 */
class TeacherController extends Controller implements IProtected
{
    /**
     * List works assigned to the teacher
     * @Route("/teacher", name="teacher_home")
     */
    public function indexAction()
    {
        return new \Symfony\Component\HttpFoundation\Response("<h1>It works!</h1>");
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
        if ($user->passwordChanged) {
            return $this->redirectToRoute("teacher_home");
        }

        $request = $this->request();
        if ($request->getMethod() == "POST") {
            $password = $request->request->get("password");
            if (!$password) {
                throw new \Exception("no password provided ".var_export($password, TRUE));
            }
            $user->initPassword($this->db(), $password);
            return $this->redirectToRoute("teacher_home");
        } else {
            return $this->render("teacher/init_password.html.twig");
        }
    }
}
