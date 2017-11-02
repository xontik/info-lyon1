<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration extends TM_Controller
{
    public function secretariat_index()
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


        //TODO differencier ce qui est modifiable
        //$UEs = $this->adminMod->getAllUEParcours();

        $this->data = array(
            'parcours' => $parcours,
            'semestres' => $outSem,
            'parcoursForSemester' => $parcoursForSemester
        );

        $this->show('Tableau de bord');
    }

    public function secretariat_semestre($semesterId)
    {
        $semesterId = intval(htmlspecialchars($semesterId));

        $this->load->model('Students_model', 'studentMod');
        $this->load->model('Semester_model', 'semMod');

        if (!$this->semMod->isSemesterEditable($semesterId)) {
            addPageNotification('Impossible d\'editer ce semestre', 'danger');
            redirect('Administration');
        }
        $semestre = $this->semMod->getSemesterById($semesterId);

        $groups = $this->studentMod->getStudentsBySemestre($semesterId);

        //false pour recuperer ceux qui non pas dutout de group sahcant qu'on a deja ceux du semestre
        $freeStudents = $this->semMod->getStudentWithoutGroup($semesterId, false);

        $idGroupe = 0;
        $outGroups = array();
        foreach ($groups as $key => $group)
        {
            if ($idGroupe != $group->idGroupe) {
                $idGroupe = $group->idGroupe;
                $outGroups[] = array(
                    'idGroupe' => $idGroupe,
                    'nomGroupe' => $group->nomGroupe,
                    'students' => array()
                );
            }

            $outGroups[count($outGroups) - 1]['students'][] = array(
                'prenom' => $group->prenom,
                'nom' => $group->nom,
                'numEtudiant' => $group->numEtudiant
            );
        }

        $this->data = array(
            'groups' => $outGroups,
            'semestre' => $semestre,
            'freeStudents' => $freeStudents
        );

        $this->show('Gestion de semestre');
    }
}
