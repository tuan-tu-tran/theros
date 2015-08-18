<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\RawData;
use AppBundle\Entity\Klass;
use AppBundle\Entity\Student;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $db=$this->get("database_connection");
        $res=$db->query("
            SELECT *
            FROM raw_data
            JOIN student ON st_id = rd_st_id
            JOIN class ON cl_id = rd_cl_id
        ")->fetchAll();
        $works=[];
        foreach($res as $row){
            $w=new RawData($row);
            $w->class=new Klass($row);
            $w->student=new Student($row);
            $works[] = $w;
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "works"=>$works,
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
