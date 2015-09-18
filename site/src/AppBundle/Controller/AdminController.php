<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Teacher;
use AppBundle\Entity\Work;

class AdminController extends Controller implements IAdminPage
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function homeAction()
    {
        Work::GetCounts($this->db(), $this->getSchoolYear(), $encoded, $total);
        return $this->render("admin/home.html.twig", array(
            "encoded" => $encoded,
            "total" => $total
        ));
    }

    /**
     * @Route("/admin/users", name="admin_user")
     */
    public function userAction()
    {
        $db = $this->db();
        $request = $this->request();
        if ($request->getMethod() == "POST") {
            $id=$request->request->get("id");
            $admin=json_decode($request->request->get("admin"));
            $teacher=Teacher::GetById($db, $id);
            if (!$teacher) {
                throw $this->createNotFoundException("no such teacher: $id");
            }
            $teacher->setAdmin($db, $admin);
            return new Response();
        }
        $teachers=Teacher::GetAll($db, TRUE);
        return $this->render("admin/users.html.twig", array(
            "teachers" => $teachers
        ));
    }

}
