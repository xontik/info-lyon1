<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
            redirect('/');

    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Tableau de bord'
        );
        show('Professeur/dashboard', $data);
    }

    public function absence()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Absences'
        );
        show('Professeur/absences', $data);
    }

    public function note()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Notes'
        );
        show('Professeur/notes', $data);
    }

    public function ptut() {
        $this->load->model('ptut_model');

        $ptuts = $this->ptut_model->getPtutsOfProf($_SESSION['id']);

        $data = array(
            'css' => array(),
            'js' => array('debug'),
            'title' => 'Projets tuteurés',
            'data' => array(
                'ptuts' => $ptuts
            )
        );
        show('Professeur/ptut', $data);
    }

    public function project($groupId = '') {
        if ($groupId === '') {
            show_404();
        }

        $this->load->model('ptut_model');
        $this->load->helper('time');

        $group = $this->ptut_model->getGroup($groupId, $_SESSION['id']);
        if (empty($group)) {
            show_404();
            // Replace by notification, group not found
        }

        $members = $this->ptut_model->getGroupMembers($groupId);
        $lastAppointement = $this->ptut_model->getLastAppointement($groupId);
        $nextAppointement = $this->ptut_model->getNextAppointement($groupId);
        $proposals = $this->ptut_model->getDateProposals($nextAppointement->idRDV);

        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Projets tuteurés',
            'data' => array(
                'group' => $group,
                'members' => $members,
                'lastAppointement' => $lastAppointement,
                'nextAppointement' => $nextAppointement,
                'proposals' => $proposals
            )
        );
        show('Professeur/project', $data);
    }

    public function edt()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Emploi du temps'
        );
        show('Professeur/edt', $data);
    }

    public function question()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Questions / Réponses'
        );
        show('Professeur/questions', $data);
    }

    public function controle()
    {
        $this->load->model('control_model', 'ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['id']);
        $matieres = $this->ctrlMod->getMatieres($_SESSION['id']);
        $groupes = $this->ctrlMod->getGroupes($_SESSION['id']);
        $typeControle = $this->ctrlMod->getTypeControle();


        $restrict = array(); //le filtre


        if (isset($_POST['filter'])) {

            if (isset($_POST['typeControle']) && $_POST['typeControle'] != 0) {
                $restrict['typeControle'] = $_POST['typeControle'];
            }
            if (isset($_POST['groupes']) && $_POST['groupes'] != 0) {
                $restrict['groupes'] = $_POST['groupes'];
            }
            if (isset($_POST['matieres']) && $_POST['matieres'] != 0) {
                $restrict['matieres'] = $_POST['matieres'];
            }

            //TODO CHECK CE FOREACH un chouilla trop suceptible

            foreach ($controls as $key => $control) {
                if (!is_null($control->nomGroupe) && isset($restrict['groupes']) && $control->idGroupe != $restrict['groupes']) {
                    unset($controls[$key]);
                }
                if (isset($restrict['matieres']) && $control->idMatiere != $restrict['matieres']) {
                    unset($controls[$key]);

                }

                if (isset($restrict['typeControle']) && $restrict['typeControle'] != $control->idTypeControle) {
                    unset($controls[$key]);

                }

            }
        }


        $css = array('Professeurs/notes');
        $js = array('debug');
        $title = 'Controles';
        $data = array('controls' => $controls, 'groupes' => $groupes, 'matieres' => $matieres, 'restrict' => $restrict, 'typeControle' => $typeControle);
        $var = array(
            'css' => $css,
            'js' => $js,
            'title' => $title,
            'data' => $data);

        show('Professeur/controles', $var);
    }

    public function addControle($promo = '')
    {
        //TODO verifier isreferent
        $this->load->model('control_model', 'ctrlMod');

        if ($promo == '') {
            $select = $this->ctrlMod->getEnseignements($_SESSION['id']);
        } else if ($promo == 'promo') {
            $select = $this->ctrlMod->getMatieres($_SESSION['id']);
        } else {
            show_404();
        }

        $typeControle = $this->ctrlMod->getTypeControle();

        $data = array(
            'css' => array('Professeurs/addDSPromo'),
            'js' => array('debug'),
            'title' => 'Ajout de controles',
            'data' => array(
                'select' => $select,
                'promo' => $promo == 'promo',
                'typeControle' => $typeControle
            )
        );

        show('Professeur/addControl', $data);
    }

    public function editControle($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }
        $this->load->model('control_model', 'ctrlMod');

        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            addPageNotification('Controle introuvable', 'danger');
            redirect('professeur/controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce controle', 'danger');
            redirect('professeur / controle');
        }

        $typeControle = $this->ctrlMod->getTypeControle();

        $data = array(
            'css' => array('Professeurs / editcontrole'),
            'js' => array('debug'),
            'title' => 'Ajout de controles',
            'data' => array(
                'control' => $control,
                'typeControle' => $typeControle
            )
        );
        show('Professeur / editControl', $data);
    }

    public function ajoutNotes($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->model('mark_model', 'markMod');

        $control = $this->ctrlMod->getControl($id);
        if (empty($control)) {
            addPageNotification('Controle introuvable', 'danger');
            redirect('professeur / controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce controle', 'danger');
            redirect('professeur/controle');
        }

        $marks = $this->markMod->getMarks($control, $_SESSION['id']);
        $matiere = $this->ctrlMod->getMatiere($id);

        $data = array(
            'css' => array('Professeurs/ajoutnotes'),
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
