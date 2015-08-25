<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction()
    {
        return $this->render("login/index.html.twig");
    }
}
