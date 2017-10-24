<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
            redirect('/');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Tableau de bord"
        );
        show("Professeur/dashboard", $data);
    }

    public function absence() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Absences"
        );
        show("Professeur/absences", $data);
    }

    public function note() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Notes"
        );
        show("Professeur/notes", $data);
    }

    public function ptut() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Projets tuteurés"
        );
        show("Professeur/ptut", $data);
    }

    public function edt() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Emploi du temps"
        );
        show("Professeur/edt", $data);
    }

    public function question() {

        $this->load->model('students_model', 'studentMod');
        $this->load->model('question_model', 'questionsMod');

        $profQuestions = $this->questionsMod->getProfessorQuestions($_SESSION['id']);
        $var = array(
            'css' => array('Professeurs/questions'),
            'js' => array('debug'),
            'title' => 'Questions',
            'data' => array('profQuestions' => $profQuestions)
        );

        show("Professeur/questions", $var);
    }

    public function controle() {
        $this->load->model('control_model', 'ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['id']);
        $matieres = $this->ctrlMod->getMatieres($_SESSION['id']);
        $groupes = $this->ctrlMod->getGroupes($_SESSION['id']);
        $typeControle = $this->ctrlMod->getTypeControle();




        $restrict = array("groupes" => array(), "matieres" => array(), "DS" => array()); //le filtre
        /*
          echo "<pre>";
          var_dump($matieres);
          echo "</pre>";
          // */

        if (isset($_POST["filter"])) {


            $grp = array(); //from bd
            $mat = array(); //from bd


            foreach ($groupes as $groupe) {
                array_push($grp, $groupe->idGroupe);
            }
            foreach ($matieres as $matiere) {
                array_push($mat, $matiere->idMatiere);
            }

            $restrict = array(); //le filtre




            if (isset($_POST["typeControle"]) && $_POST["typeControle"] != 0) {
                $restrict["typeControle"] = $_POST["typeControle"];
            }
            if (isset($_POST["groupes"]) && $_POST["groupes"] != 0) {
                $restrict["groupes"] = $_POST["groupes"];
            }
            if (isset($_POST["matieres"]) && $_POST["matieres"] != 0) {
                $restrict["matieres"] = $_POST["matieres"];
            }

            //TODO CHECK CE FOREACH un chouilla trop suceptible

            foreach ($controls as $key => $control) {
                if (!is_null($control->nomGroupe) && isset($restrict["groupes"]) && $control->idGroupe != $restrict["groupes"]) {
                    unset($controls[$key]);
                }
                if (isset($restrict["matieres"]) && $control->idMatiere != $restrict["matieres"]) {
                    unset($controls[$key]);
                }

                if (isset($restrict["typeControle"]) && $restrict["typeControle"] != $control->idTypeControle) {
                    unset($controls[$key]);
                }
            }
        }


        $css = array("Professeurs/notes");
        $js = array("debug");
        $title = "Controles";
        $data = array("controls" => $controls, "groupes" => $groupes, "matieres" => $matieres, "restrict" => $restrict, "typeControle" => $typeControle);
        $var = array(
            "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/controles", $var);
    }

    public function addControle($promo = "") {
        //TODO verifier isreferent
        $this->load->model('control_model', 'ctrlMod');
        $bool = false;
        if ($promo == "") {
            $select = $this->ctrlMod->getEnseignements($_SESSION['id']);
        } else if ($promo == "promo") {
            $bool = true;
            $select = $this->ctrlMod->getMatieres($_SESSION['id']);
        } else {
            show_404();
            return;
        }
        $typeControle = $this->ctrlMod->getTypeControle();


        $css = array("Professeurs/addDSPromo");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("select" => $select, "promo" => $bool, 'typeControle' => $typeControle);
        $var = array(
            "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/addControl", $var);
    }

    public function editControle($id = "") {
        if ($id == "") {
            show_404();
        }
        $this->load->model('control_model', 'ctrlMod');
        $typeControle = $this->ctrlMod->getTypeControle();


        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            $this->session->set_flashdata("notif", array("Controle Introuvable"));
            redirect("professeur/controle");
        }
        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/controle");
        }



        $css = array("Professeurs/editcontrole");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("control" => $control, 'typeControle' => $typeControle);
        $var = array("css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/editControl", $var);
    }

    public function ajoutNotes($id = "") {
        if ($id == "") {
            echo "d";

            show_404();
        }
        $this->load->model('control_model', 'ctrlMod');
        $this->load->model('mark_model', 'markMod');

        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            $this->session->set_flashdata("notif", array("Controle Introuvable"));
            redirect("professeur/controle");
        }
        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/controle");
        }

        $marks = $this->markMod->getMarks($control, $_SESSION["id"]);

        $css = array("Professeurs/ajoutnotes");
        $matiere = $this->ctrlMod->getMatiere($id);
        $js = array("debug");
        $title = "Ajout de notes";
        $data = array("control" => $control, "marks" => $marks, "matiere" => $matiere);
        $var = array("css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/addMarks", $var);
    }

}
