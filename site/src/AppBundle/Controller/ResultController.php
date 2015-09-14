<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ResultController extends Controller implements IAdminPage
{
    /**
     * @Route("/results", name="result_list")
     */
    public function listAction()
    {
        return $this->render("result/list.html.twig");
    }
}
