<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\RawData;
use AppBundle\Entity\Klass;
use AppBundle\Entity\Student;
use AppBundle\Entity\Subject;
use AppBundle\Entity\Teaching;
use AppBundle\Entity\Work;

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
        $id=$request->request->get("id");
        return $this->getDetails($id);
    }

    private function getDetails($id)
    {
        $db=$this->get("database_connection");
        //get requested raw data
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

        //get list of current works
        $s=$db->prepare("
            SELECT *
            FROM work
            JOIN raw_data_work ON rdw_w_id = w_id
            JOIN subject ON w_sub_id = sub_id
            JOIN schoolyear ON w_sy_id = sy_id
            LEFT JOIN teacher ON tea_id = w_tea_id
            JOIN student ON st_id = w_st_id
            WHERE rdw_rd_id = :id
        ");
        $s->bindValue("id",$id, \PDO::PARAM_INT);
        $s->execute();
        $result = $s->fetchAll();
        $works=[];
        foreach($result as $row){
            $works[]=Work::GetFull($row);
        }

        //get list of subjects
        $s=$db->prepare("
            SELECT DISTINCT s.*
            FROM teacher_subject
            JOIN subject s ON ts_sub_id = sub_id
            JOIN schoolyear sy ON ts_sy_id = sy_id
            WHERE ts_cl_id = :cl_id
            AND sy_desc = :schoolyear
            ORDER BY sub_code, sub_desc
        ");
        $schoolyear = $this->getParameter("schoolyear");
        $s->bindValue("cl_id", $raw->class->id, \PDO::PARAM_INT);
        $s->bindValue("schoolyear", $schoolyear, \PDO::PARAM_STR);
        $s->execute();
        $result=$s->fetchAll();
        $subjects=[];
        foreach($result as $row){
            $subjects[] = new Subject($row);
        }

        return $this->render("default/details.html.twig", array(
            "raw"=>$raw,
            "subjects"=>$subjects,
            "works"=>$works,
        ));
    }

    /**
     * @Route("/add", name="add")
     */
    public function addAction(Request $request)
    {
        $studentId = $request->request->get("studentId");
        $teachingId = $request->request->get("teachingId");
        $rawDataId = $request->request->get("rawDataId");
        $description = $request->request->get("description");
        $type = $request->request->get("type");

        $db=$this->get("database_connection");

        $db->beginTransaction();
        try
        {
            $s=$db->prepare("INSERT INTO work(w_type, w_ts_id, w_st_id, w_description) VALUES (:type, :teachingId, :studentId, :description)");
            $s->bindValue("type", $type, \PDO::PARAM_INT);
            $s->bindValue("teachingId", $teachingId, \PDO::PARAM_INT);
            $s->bindValue("studentId", $studentId, \PDO::PARAM_INT);
            $s->bindValue("description", $description, \PDO::PARAM_STR);
            $s->execute();

            $workId = $db->lastInsertId();
            $s=$db->prepare("INSERT INTO raw_data_work(rdw_rd_id, rdw_w_id) VALUES (:rawDataId, :workId)");
            $s->bindValue("rawDataId", $rawDataId, \PDO::PARAM_INT);
            $s->bindValue("workId", $workId, \PDO::PARAM_INT);
            $s->execute();

            $db->commit();
        }
        catch(\Exception $e)
        {
            $db->rollBack();
            throw $e;
        }
        return $this->getDetails($rawDataId);
    }

    /**
     * @Route("/delete", name="delete")
     * @Method({"POST"})
     */
    public function deleteAction(Request $request)
    {
        $workId = $request->request->get("workId");
        if($workId){
            $db=$this->get("database_connection");
            $s=$db->prepare("DELETE FROM work WHERE w_id = :workId");
            $s->bindValue("workId", $workId, \PDO::PARAM_INT);
            $s->execute();
            return new Response();
        } else {
            throw new \Exception("No work id provided");
        }
    }

    /**
     * @Route("/treat", name="treat")
     * @Method({"POST"})
     */
    public function toggleTreated(Request $request)
    {
        $id=$request->request->get("id");
        if($id)
        {
            $db=$this->get("database_connection");
            $s=$db->prepare("UPDATE raw_data SET rd_treated = !rd_treated WHERE rd_id = :id");
            $s->bindValue("id", $id, \PDO::PARAM_INT);
            $s->execute();
            return new Response();
        }
        else{
            throw new \Exception("no id provided");
        }
    }
}
