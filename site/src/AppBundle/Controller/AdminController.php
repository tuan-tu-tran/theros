<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Teacher;

class AdminController extends Controller implements IAdminPage
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function homeAction()
    {
        return $this->render("admin/home.html.twig");
    }

    /**
     * @Route("/admin/users", name="admin_user")
     */
    public function userAction()
    {
        $db = $this->db();
        $teachers=Teacher::GetAll($db);
        return $this->render("admin/users.html.twig", array(
            "teachers" => $teachers
        ));
    }

}
