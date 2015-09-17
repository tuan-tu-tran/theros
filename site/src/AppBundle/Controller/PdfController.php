<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use fpdf\FPDF;

use AppBundle\Entity\Student;
use AppBundle\Entity\Klass;
use AppBundle\Entity\Work;

class PdfController extends Controller
{
    static $months=array(
        null,
        "janvier",
        "février",
        "mars",
        "avril",
        "mai",
        "juin",
        "juillet",
        "août",
        "septembre",
        "octobre",
        "novembre",
        "décembre",
    );
    /**
     * @Route("/pdf")
     */
    public function indexAction()
    {
        $db = $this->db();
        $student = new Student($row = $db->query("
            SELECT *
            FROM student
            JOIN student_class ON sc_st_id = st_id
            JOIN class ON sc_cl_id = cl_id
            WHERE st_name like 'Pepa%'
        ")->fetch());
        $student->class = new Klass($row);

        $works = array();
        foreach ($db->query("SELECT w_id FROM work WHERE w_st_id = ".$student->id." AND w_has_result") as $row) {
            $works[] = Work::GetFullById($db, $row["w_id"]);
        }
        $schoolyear = $this->getSchoolYear();

        $pageWidth=210;
        $pageHeight=297;
        $rightMargin=10;
        $contentWidth=$pageWidth - 2*$rightMargin;
        $height=5;
        $pdf = new FPDF();
        $pdf->SetMargins($rightMargin, $rightMargin, $rightMargin);
        $pdf->AddPage();
        $pdf->SetFont("Times");
        $pdf->Image($this->webDir("img/logo.png"), $pdf->getX(), $pdf->getY(), 20, 20);
        $pdf->SetFont("", "B");
        $pdf->Cell(0, $height, "Institut Saint-Dominique - Section secondaire", 0, 1, "C");
        $pdf->SetFont("", "");
        $pdf->MultiCell(0, $height, "Rue Caporal Claes, 38 - 1030 Bruxelles\nTel.: 02/240.14.10 - Fax: 02/240.16.11", 0, "C");
        $email="saintdominique@ens.irisnet.be";
        $site = "www.saintdominique.be";
        $width = $pdf->GetStringWidth("$email - $site");
        $pdf->SetX($pdf->GetX() + ($pageWidth - $pdf->getX() - $rightMargin - $width)/2);
        $pdf->Write($height, $email, "mailto:$email");
        $pdf->Write($height, " - ");
        $pdf->Write($height, $site, $site);
        $pdf->Ln();
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+$contentWidth, $pdf->GetY());
        $pdf->Ln();
        $pdf->SetY(42);
        $pdf->SetX(112);
        $tutor = $student->tutor;
        $address = $student->address;
        $zip = $student->zip;
        $city = $student->city;
        $pdf->MultiCell(0, $height, utf8_decode("$tutor\n$address\n$zip - $city"));
        $date = date("j ").utf8_decode(self::$months[date("n")]).date(" Y");
        $pdf->SetY(75);
        $pdf->Cell(0, $height, "Schaerbeek, le $date", 0, 1, "R");
        $name=$student->name;
        $pdf->SetY(90);
        $pdf->MultiCell(0, $height, utf8_decode("Chers parents, cher élève,
 

Vous trouverez ci-joint les résultats des travaux de vacances et remises à niveau de $name.

Si votre enfant est en réussite, le contrat a été rempli et nous considérons que les lacunes observées en juin ont été totalement ou partiellement levées (voir commentaire éventuel laissé par le professeur).

En cas d'échec, par contre, nous encourageons l'élève à poursuivre le travail de remédiation dans les matières concernées. En effet, vous savez que l'accumulation d'échecs dans une même discipline compromet le bon déroulement des apprentissages.


Valériane Wiot et Vincent Sterpin
"));
        $pdf->AddPage();
        $pdf->SetFont("", "B");
        $pdf->Write($height, utf8_decode("Fiche de résultats $schoolyear: ".$student->name." [".$student->class->code."]"));
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont("", "");
        foreach ($works as $w) {
            $content="";
            $tdv = $w->isTdv();
            $type = $tdv ? "Travail de vacances":"Remise à niveau";
            $subject = $w->subject->description." [".$w->subject->code."]";
            $teacherName = $w->teacher->fullname;
            if ($w->result) {
                $result = $w->result;
                if (!$tdv) {
                    $result.="/100";
                }
            } else {
                 $result=$tdv?"Non rendu":"Absent";
            }
            $content = "$type en $subject

Professeur: $teacherName

Résultat: $result
";
            if ($w->remark!==NULL && $w->remark!="") {
                $content.="\nCommentaire du professeur:\n";
                $content.=rtrim($w->remark);
            }
            $remainingSpace = $pageHeight - $pdf->GetY() - $rightMargin;
            $linesCount = substr_count($content,PHP_EOL) +1;
            dump(sprintf("remaining space: %d, lines count %d, must break: %d, content:\n%s", $remainingSpace, $linesCount, $remainingSpace < $linesCount * $height, $content));
            if($remainingSpace < $linesCount * $height) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(0, $height, utf8_decode($content), 1);
            $pdf->Ln();
        }
        foreach ($works as $w) {
            $pdf->AddPage();
            $pdf->SetFont("", "B");
            $pdf->Write($height, utf8_decode("Fiche de résultat $schoolyear: ".$w->student->name." [".$w->student->class->code."]"));
            $pdf->Ln();
            $tdv = $w->isTdv();
            $type = $tdv ? "Travail de vacances":"Remise à niveau";
            $subject = $w->subject->description." [".$w->subject->code."]";
            $pdf->SetFont("", "");
            $pdf->Ln();
            $pdf->Write($height, utf8_decode("$type en $subject"));
            $pdf->Ln();
            $pdf->Ln();
            $teacherName = $w->teacher->fullname;
            $pdf->Write($height, utf8_decode("Professeur: $teacherName"));
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Write($height, utf8_decode("Résultat: "));
            if ($w->result) {
                $pdf->Write($height, $w->result);
                if (!$tdv) {
                    $pdf->Write($height, "/100");
                }
            } else {
                $pdf->Write($height, $tdv?"Non rendu":"Absent");
            }
            $pdf->Ln();
            if ($w->remark!==NULL && $w->remark!="") {
                $pdf->Ln();
                $pdf->Write($height, "Commentaire du professeur:");
                $pdf->Ln();
                $pdf->MultiCell(0, $height, utf8_decode($w->remark));
            }
        }

        $response = new Response();
        $response->headers->set("Content-Type","application/pdf");
        $response->setContent($pdf->Output(null,"S"));
        return $response;
    }
}