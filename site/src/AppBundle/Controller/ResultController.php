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
        $schoolyear = $this->getSchoolYear();
        $works = Work::GetListBySchoolYear($db, $schoolyear);

        $studentIds=array();
        $students=array();
        $classIds = array();
        $classes = array();
        $teacherIds = array();
        $teachers = array();
        foreach ($works as $w) {
            self::addIfNotIn($w->student, $studentIds, $students);
            self::addIfNotIn($w->student->class, $classIds, $classes);
            if ($w->teacher) {
                self::addIfNotIn($w->teacher, $teacherIds, $teachers);
            }
        }

        usort($students, function($x, $y){
            return strcmp($x->name, $y->name);
        });

        usort($classes, function($x, $y){
            return strcmp($x->code, $y->code);
        });

        usort($teachers, function($x, $y){
            return strcmp($x->fullname, $y->fullname);
        });


        return $this->render("result/list.html.twig", array(
            "works" => $works
            , "students" => $students
            , "classes" => $classes
            , "teachers" => $teachers
        ));
    }

    private static function addIfNotIn($s, &$ids, &$array)
    {
        if (!isset($ids[$s->id])) {
            $array[]=$s;
            $ids[$s->id] = TRUE;
        }
    }
}
