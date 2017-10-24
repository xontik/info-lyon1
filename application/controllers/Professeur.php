<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
            redirect('/');
    }
    
    public function index() {
        $this->absence();
    }
    
    public function absence() {
        $data = array(
            'css' => array('Professeur/absences'),
            'js' => array(),
            'page' => 'absences',
            'title' => 'Absences'
        );
        show('Professeur/absences', $data);
    }
    
    public function note() {
        $data = array(
            'css' => array('Professeur/notes'),
            'js' => array(),
            'page' => 'notes',
            'title' => 'Notes'
        );
        show('Professeur/notes', $data);
    }
    
    public function ptut() {
        $data = array(
            'css' => array('Professeur/ptut'),
            'js' => array(),
            'page' => 'ptut',
            'title' => 'Projets tuteurés'
        );
        show('Professeur/ptut', $data);
    }
    
    public function edt() {
        $data = array(
            'css' => array('Professeur/edt'),
            'js' => array(),
            'page' => 'edt',
            'title' => 'Emploi du temps'
        );
        show('Professeur/edt', $data);
    }
    
    public function question() {
        $this->load->model('students_model', 'studentMod');
        $this->load->model('question_model', 'questionsMod');

        $profQuestions = $this->questionsMod->getProfessorQuestions($_SESSION['id']);

        $data = array(
            'css' => array('Professeurs/questions'),
            'js' => array('debug'),
            'title' => 'Questions',
            'data' => array('profQuestions' => $profQuestions)
        );
        show("Professeur/questions", $data);
    }
    
    public function controle() {
        $this->load->model('control_model','ctrlMod');
        
        $controls = $this->ctrlMod->getControls($_SESSION['id']);
        $matieres = $this->ctrlMod->getMatieres($_SESSION['id']);
        $groupes = $this->ctrlMod->getGroupes($_SESSION['id']);
        $typeControle = $this->ctrlMod->getTypeControle();
        
        $restrict = array(
            'typeControle' => isset($_POST['typeControle']) ? intval(htmlspecialchars($_POST['typeControle'])) : 0,
            'groupes' => isset($_POST['groupes']) ? intval(htmlspecialchars($_POST['groupes'])) : 0,
            'matieres' => isset($_POST['matieres']) ? intval(htmlspecialchars($_POST['matieres'])) : 0
        );
        
        foreach ($controls as $key => $control) {
            if (!is_null($control->nomGroupe)
                && $restrict['groupes'] !== 0
                && $control->idGroupe != $restrict['groupes']
            ) {
                unset($controls[$key]);
            }
            
            if ($restrict['matieres'] !== 0
                && $control->idMatiere != $restrict['matieres']
            ) {
                unset($controls[$key]);
            }
            
            if ($restrict['typeControle'] !== 0
                && $restrict['typeControle'] != $control->idTypeControle
            ) {
                unset($controls[$key]);
            }
        }
        
        
        $data = array(
            'css' => array(),
            'js' => array('debug'),
            'page' => 'controles',
            'title' => 'Controles',
            'data' => array(
                'controls' => $controls,
                'groupes' => $groupes,
                'matieres' => $matieres,
                'restrict' => $restrict,
                'typeControle' => $typeControle
            )
        );
        
        show('Professeur/controles', $data);
    }
    
    public function addControle($promo = '')
    {
        $this->load->model('control_model', 'ctrlMod');

        $isPromo = strtolower($promo) === 'promo';

        if ($promo === '') {
            $select = $this->ctrlMod->getEnseignements($_SESSION['id']);
        } else if ($isPromo) {
            $select = $this->ctrlMod->getMatieres($_SESSION['id']);
        } else {
            show_404();
            return;
        }

        $typeControle = $this->ctrlMod->getTypeControle();

        $data = array(
            'css' => array(),
            'js' => array('debug', 'Professeur/ajoutControle'),
            'title' => 'Ajout de controles',
            'data' => array(
                'select' => $select,
                'promo' => $isPromo,
                'typeControle' => $typeControle
            )
        );
        show('Professeur/addControle', $data);
    }

    public function editControle($id = '') {
        if ($id == '') {
            show_404();
        }

        $this->load->model('control_model','ctrlMod');
        $typeControle = $this->ctrlMod->getTypeControle();

        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            $this->session->set_flashdata('notif', array('Controle Introuvable'));
            redirect('professeur/controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            $this->session->set_flashdata('notif', array('Vous n\'avez pas les droit sur ce controle'));
            redirect('professeur/controle');
        }

        $data = array(
            'css' => array('Professeur/editcontrole'),
            'js' => array('debug'),
            'title' => 'Ajout de controles',
            'data' => array(
                'control' => $control,
                'typeControle' => $typeControle
            )
        );

        show('Professeur/editControl', $data);
    }
    
    public function ajoutNotes($id = '') {
        if ($id === '') {
            show_404();
        }

        $this->load->model('control_model','ctrlMod');
        $this->load->model('mark_model','markMod');
        
        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            $this->session->set_flashdata('notif', array('Controle Introuvable'));
            redirect('professeur/controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            $this->session->set_flashdata('notif', array('Vous n\'avez pas les droit sur ce controle'));
            redirect('professeur/controle');
        }
        
        $marks = $this->markMod->getMarks($control,$_SESSION['id']);
        $matiere = $this->ctrlMod->getMatiere($id);

        $data = array(
            'css' => array(),
            'js' => array('debug'),
            'title' => 'Ajout de notes',
            'data' => array(
                'control' => $control,
                'marks' => $marks,
                'matiere' => $matiere
            )
        );
        
        show('Professeur/addMarks', $data);
    }
}
