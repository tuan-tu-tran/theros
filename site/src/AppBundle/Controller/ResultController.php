<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Work;

class ResultController extends Controller implements IAdminPage
{
    /**
     * @Route("/results", name="result_list")
     */
    public function listAction()
    {
        $db = $this->db();
        $works = Work::GetListBySchoolYear($db, $this->getSchoolYear());
        return $this->render("result/list.html.twig", array(
            "works" => $works
        ));
    }
}
