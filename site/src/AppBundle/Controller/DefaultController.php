<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\RawData;
use AppBundle\Entity\Klass;
use AppBundle\Entity\Student;
use AppBundle\Entity\Subject;
use AppBundle\Entity\Teaching;

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
            $w=RawData::GetFull($row);
            $works[] = $w;
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "works"=>$works,
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/details", name="details")
     */
    public function detailsAction(Request $request)
    {
        $id=$request->query->get("id");

        $db=$this->get("database_connection");
        $stmt=$db->prepare("
            SELECT *
            FROM raw_data
            JOIN student ON st_id = rd_st_id
            JOIN class ON cl_id = rd_cl_id
            WHERE rd_id = :id
        ");
        $stmt->bindValue("id",$id, \PDO::PARAM_INT);
        $stmt->execute();
        $result=$stmt->fetch();
        $raw = RawData::GetFull($result);

        $s=$db->prepare("
            SELECT *
            FROM teacher_subject
            JOIN subject ON ts_sub_id = sub_id
            JOIN schoolyear ON ts_sy_id = sy_id
            JOIN class ON cl_id = ts_cl_id
            JOIN teacher ON tea_id = ts_tea_id
            WHERE ts_cl_id = :cl_id
            AND sy_desc = :schoolyear
            ORDER BY sub_code
        ");
        $schoolyear = $this->getParameter("schoolyear");
        $s->bindValue("cl_id", $raw->class->id, \PDO::PARAM_INT);
        $s->bindValue("schoolyear", $schoolyear, \PDO::PARAM_STR);
        $s->execute();
        $result=$s->fetchAll();
        $teachings=[];
        foreach($result as $row){
            $teachings[] = Teaching::GetFull($row);
        }

        return $this->render("default/details.html.twig", array(
            "raw"=>$raw,
            "teachings"=>$teachings,
        ));
    }
}
