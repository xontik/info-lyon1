<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretariat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function index()
    {
        $this->absence();
    }

    public function absence()
    {
        $this->load->model('absence_model');
        $this->load->model('semester_model');
        $this->load->model('students_model');

        $this->load->helper('time');

        $period = $this->semester_model->getCurrentPeriod();
        $students = $this->students_model->getStudentsOrganized();
        $absences = $this->absence_model->getAbsencesInPeriod($period);

        // Associate absence to the student
        $abs_assoc = array();
        foreach ($absences as $absence) {
            $abs_assoc[$absence->numEtudiant][] = $absence;
        }

        // Associate students absences to the day it happened
        $groups = array();
        $assoc = array();

        foreach ($students as $student) {
            if (!isset($assoc[$student->numEtudiant])) {
                $assoc[$student->numEtudiant] = array(
                    'numEtudiant' => $student->numEtudiant,
                    'nom' => $student->nom,
                    'prenom' => $student->prenom,
                    'mail' => $student->mail,
                    'groupe' => $student->nomGroupe,
                    'absences' => array(
                        'total' => 0,
                        'total_days' => 0,
                        'justified' => 0
                    )
                );

                if (isset($groups[$student->nomGroupe])) {
                    $groups[$student->nomGroupe] += 1;
                } else {
                    $groups[$student->nomGroupe] = 1;
                }
            }

            if (isset($abs_assoc[$student->numEtudiant])) {

                $assoc[$student->numEtudiant]['absences']['justified'] = 0;

                foreach ($abs_assoc[$student->numEtudiant] as $absence) {
                    $index = $period->getDays(new DateTime($absence->dateDebut));
                    $assoc[$student->numEtudiant]['absences'][$index][] = $absence;

                    if ($absence->justifiee) {
                        $assoc[$student->numEtudiant]['absences']['justified'] += 1;
                    }
                }

                $assoc[$student->numEtudiant]['absences']['total'] =
                    count($abs_assoc[$student->numEtudiant]);
                $assoc[$student->numEtudiant]['absences']['total_days'] =
                    count($assoc[$student->numEtudiant]['absences']) - 3;
            }
        }

        $data = array(
            'css' => array('Secretariat/absences'),
            'js' => array('debug', 'absences_table'),
            'page' => 'absences',
            'title' => 'Absences',
            'data' => array(
                'absences' => $assoc,
                'groups' => $groups,
                'begin_date' => $period->getBeginDate(),
                'day_number' => $period->getDays(),
                'absenceTypes' => $this->absence_model->getAbsenceTypes()
            )
        );
        show('Secretariat/absences', $data);
    }


    public function gestionSemestre($id)
    {
        $this->load->model('Students_model', 'studentMod');
        $this->load->model('Semester_model', 'semMod');
        if (!$this->semMod->isSemesterEditable($id)) {
            $this->session->set_flashdata('notif', array('Impossible d\'editer ce semestre'));
            redirect('secretariat/administration');
        }
        $semestre = $this->semMod->getSemesterById($id);

        $groups = $this->studentMod->getStudentsBySemestre($id);

        //false pour recuperer ceux qui non pas dutout de group sahcant qu'on a deja ceux du semestre
        $freeStudents = $this->semMod->getStudentWithoutGroup($id, false);

        $idGroupe = 0;
        $outGroups = array();
        foreach ($groups as $key => $group)
        {
            if ($idGroupe != $group->idGroupe) {
                $idGroupe = $group->idGroupe;
                $outGroups[] = array('idGroupe' => $idGroupe, 'nomGroupe' => $group->nomGroupe, 'students' => array());
            }

            $outGroups[count($outGroups) - 1]['students'][] = array(
                'prenom' => $group->prenom,
                'nom' => $group->nom,
                'numEtudiant' => $group->numEtudiant
            );
        }

        $data = array(
            'css' => array(),
            'js' => array('debug', 'gestionSemestre'),
            'title' => 'Gestion du semestre',
            'data' => array('groups' => $outGroups, 'semestre' => $semestre, 'freeStudents' => $freeStudents)
        );
        show('Secretariat/semestre', $data);
    }

    public function administration()
    {
        $this->load->model('Administration_model', 'adminMod');
        $this->load->model('Semester_model', 'semMod');
        
        $parcours = $this->adminMod->getAllParcoursEditable();
        $parcoursForSemester = $this->adminMod->getAllLastParcours();
        $semestres = $this->semMod->getAllSemesters();
        
        $outSem = array();
        $idSemestre = 0;
        
        foreach ($semestres as $key => $semestre)
        {
            if ($idSemestre != $semestre->idSemestre) {
                $idSemestre = $semestre->idSemestre;
                $dateSem = $this->semMod->getSemesterPeriod($semestre->idSemestre);
                
                $now = new DateTime();
                $dateStart = $dateSem->getBeginDate();
                $dateEnd = $dateSem->getEndDate();
                
                if ($now > $dateEnd) {
                    $etat = 'after';
                } else if ($now > $dateStart) {
                    $etat = 'now';
                } else {
                    $etat = 'before';
                }
                
                $outSem[] = array('data' => $semestre,
                    'etat' => $etat,
                    'period' => $dateSem,
                    'groups' => array()
                );
            }

            if (!is_null($semestre->idGroupe)) {
                $outSem[count($outSem) - 1]['groups'][] = array('idGroupe' => $semestre->idGroupe, 'nomGroupe' => $semestre->nomGroupe);
            }
        }

        usort($outSem, function ($a, $b) {
            if ($a['period']->getBeginDate() < $b['period']->getEndDate()) {
                return 1;
            } else {
                return -1;
            }
        });


        //TODO differencié ce qui est modifiable
        $UEs = $this->adminMod->getAllUEParcours();

        $data = array(
            'css' => array(),
            'js' => array('debug', 'gestionParcours'),
            'page' => 'administration',
            'title' => 'Tableau de bord',
            'data' => array(
                'parcours' => $parcours,
                'semestres' => $outSem,
                'parcoursForSemester' => $parcoursForSemester
            )
        );
        show('Secretariat/administration', $data);
    }

}
