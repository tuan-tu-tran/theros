<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Work;
use AppBundle\Entity\Subject;
use AppBundle\Entity\Klass;

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
        $teacher=$this->user();
        $works=[];
        $db=$this->db();
        $s=$db->prepare("
            SELECT DISTINCT w.*, st.*, sy.*, cl.*, sub.*, t.*
            FROM teacher_subject
            JOIN schoolyear sy
                ON ts_sy_id = sy_id
                AND sy_desc = :schoolyear
                AND ts_tea_id = :id
            JOIN class cl
                ON cl_id = ts_cl_id
            JOIN student_class
                ON sc_cl_id = cl_id
                AND sc_sy_id = sy_id
            JOIN student st
                ON st_id = sc_st_id
            JOIN work w
                ON w_sy_id = sy_id
                AND w_st_id = st_id
            JOIN subject sub
                ON w_sub_id = sub_id
            LEFT JOIN teacher t
                ON tea_id = w_tea_id
            ORDER BY cl_desc, st_name, sub_code
        ");
        $s->bindValue("id",$teacher->id, \PDO::PARAM_INT);
        $s->bindValue("schoolyear",$this->getSchoolYear(), \PDO::PARAM_STR);
        $s->execute();
        $result=$s->fetchAll();
        foreach ($result as $row) {
            $w=Work::GetFull($row);
            $w->class=new Klass($row);
            $works[]=$w;
        }

        $subjects=[];
        if (!$works) {
            $s=$db->prepare("
                SELECT *
                FROM subject
                JOIN teacher_subject
                    ON ts_sub_id = sub_id
                    AND ts_tea_id = :id
                JOIN class ON ts_cl_id = cl_id
                JOIN schoolyear ON sy_id = ts_sy_id
                WHERE sy_desc = :schoolyear
                ORDER BY sub_code, cl_desc
            ");
            $s->bindValue("id",$teacher->id, \PDO::PARAM_INT);
            $s->bindValue("schoolyear",$this->getSchoolYear(), \PDO::PARAM_STR);
            $s->execute();
            $result=$s->fetchAll();
            $currentSubject=NULL;
            foreach ($result as $row) {
                $s=new Subject($row);
                if (!$currentSubject || $s->id != $currentSubject->id) {
                    $currentSubject = $s;
                    $subjects[]=$currentSubject;
                    $s->classes=[];
                }
                $currentSubject->classes[]=new Klass($row);
            }
        }

        return $this->render("teacher/index.html.twig", array(
            "teacher"=>$teacher
            , "works"=>$works
            , "subjects"=>$subjects
            , "errors" => $this->flash()->get("errors")
        ));
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

    /**
     * Encode the results for a work
     *
     * @Route("/encode-result/{id}", name="encode_result", requirements={"id":"\d+"})
     */
    public function encodeResultAction($id)
    {
        $db=$this->db();
        $teacher=$this->user();
        $work = Work::GetFullById($db, $id);
        if (!$work) {
            throw $this->createNotFoundException("no such work: $id");
        }
        //if work already assigned to other teacher, show error message
        if ( $work->teacher && $work->teacher->id != $teacher->id ) {
            $this->flash()->add("errors", "Le travail sélectionné est déjà attribué à ".$work->teacher->fullname.".");
            return $this->redirectToRoute("teacher_home");
        }
        $request = $this->request();
        if ( $request->getMethod() == "POST" ) {
            $type = $request->request->get("type");
            $work->subject = $request->request->get("subjectId");
            $work->description = $request->request->get("description");
            $hasResult = (bool)$request->request->get("hasResult");

            if ( $type != "1" && $type != "2" ) {
                throw new \Exception("bad type: $type while updating work $id by ".$teacher->id);
            }
            $work->type=$type;
            if ( !$hasResult ) {
                $work->result = NULL;
            } else {
                $work->result = $request->request->get("result");
            }
            $work->hasResult = TRUE;
            $work->teacher = $teacher;
            $work->update($db);
            return $this->redirectToRoute("teacher_home");
        }
        $schoolyear = $this->getSchoolYear();
        $classId = $work->student->class->id;
        $subjects=$teacher->getSubjects($db, $schoolyear, $classId);
        if (!$subjects) {
            throw new \Exception("teacher ".$teacher->id." does not teach in class $classId");
        }

        return $this->render("teacher/encode_result.html.twig", array(
            "work"=>$work
            , "subjects" => $subjects
        ));
    }

    /**
     * Reset the result of a work (and relinquish its ownership)
     *
     * @Route("/reset-result/{id}", name="reset_result", requirements={"id":"\d+"})
     * @Method({"POST"})
     */
    public function resetResult($id)
    {
        $db=$this->db();
        $teacher=$this->user();
        $work = Work::GetFullById($db, $id);
        if (!$work) {
            throw $this->createNotFoundException("no such work: $id");
        }
        //if work already assigned to other teacher, show error message
        if ( $work->teacher && $work->teacher->id != $teacher->id ) {
            $this->flash()->add("errors", "Le travail sélectionné est déjà attribué à ".$work->teacher->fullname.".");
        }
        $work->hasResult=FALSE;
        $work->result=NULL;
        $work->teacher = NULL;
        $work->update($db);
        return $this->redirectToRoute("teacher_home");
    }
}
