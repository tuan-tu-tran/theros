<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller implements IAdminPage
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function homeAction()
    {
        return $this->render("admin/home.html.twig");
    }

}
