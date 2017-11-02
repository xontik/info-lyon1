<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Administration extends CI_Controller
{
    /*
     * AJAX
     */
    public function get_UEs()
    {
        $this->load->model('administration_model', 'adminMod');

        $idParcours = intval(htmlspecialchars($_POST['idParcours']));

        $UEsIn = $this->adminMod->getUEInParcours($idParcours);
        $UEsOut = $this->adminMod->getUENotInParcours($idParcours);

        $output = array(
            'in' => $UEsIn,
            'out' => $UEsOut
        );

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function getCSVGroupeSemestre($semesterId)
    {
        $semesterId = intval(htmlspecialchars($semesterId));

        $this->load->model('Students_model', 'studentMod');
        $this->load->model('Semester_model', 'semMod');

        $semestre = $this->semMod->getSemesterById($semesterId);

        $groups = $this->studentMod->getStudentsBySemestre($semesterId);
        header('Content-Type: text/csv');
        header('Content-Encoding: UTF-8');
        header('Content-disposition: attachment; filename=' . $semestre->anneeScolaire . '-' . $semestre->type . '.csv');


        echo 'SEMESTRE;' . $semestre->idSemestre . ';<--Donnees non modifiable;;;' . PHP_EOL;
        echo 'Type du semestre;' . $semestre->type . ';Annee scolaire;' . $semestre->anneeScolaire . '-' . (((int)$semestre->anneeScolaire) + 1) . ';<--Donnees non modifiable;' . PHP_EOL;
        $idgroupe = 0;
        foreach ($groups as $group) {
            if ($idgroupe != $group->idGroupe) {
                $idgroupe = $group->idGroupe;
                echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
                echo 'GROUPE;' . $group->idGroupe . ';Nom du groupe;' . $group->nomGroupe . ';<--Donnees non modifiable;' . PHP_EOL;
            }
            echo $group->numEtudiant . ';' . $group->nom . ';' . $group->prenom . ';;;' . PHP_EOL;
        }
    }

    public function importCSV($idRedirect)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');
        $this->load->model('students_model', 'studentMod');

        if (isset($_FILES['import']) && $_FILES['import']['size'] > 0)
        {
            $format = strtolower(array_slice(
                explode('.', $_FILES['import']['name']), -1)[0]);
            if ($format === 'csv')
            {
                $csv = array();

                ini_set('auto_detect_line_endings', TRUE);
                $file = fopen($_FILES['import']['tmp_name'], 'r');

                while ($line = fgetcsv($file, 0, ';')) {
                    if ($line[0]) {
                        $csv[] = $line;
                    }
                }

                fclose($file);
                ini_set('auto_detect_line_endings', FALSE);


                $idSemestre = $csv[0][1];
                $alreadyAddedIds = array();
                $groupStudentIds = array();

                $refus = array();

                $conserve = 0;
                $delete = 0;
                $ajout = 0;


                if ($this->semMod->isSemesterEditable($idSemestre))
                {
                    $semestreDuringSamePeriod = $this->semMod->getSemesterIdsSamePeriod($idSemestre);

                    $groupId = 0;
                    foreach ($csv as $line) {

                        if ($line[0] == 'GROUPE') {
                            if ($groupId != 0) {
                                foreach ($groupStudentIds as $studentId) {
                                    $this->studentMod->deleteRelationGroupStudent($groupId, $studentId);
                                    $delete++;
                                }
                            }
                            $groupId = $line[1];
                            if (!$this->semMod->isThisGroupInSemester($groupId, $idSemestre)) {
                                $groupId = 0;
                            }
                            $groupStudentIds = $this->studentMod->getIdsFromGroup($groupId);

                        } else {
                            if ($groupId != 0) {
                                $student = array('numEtudiant' => $line[0], 'nom' => $line[1], 'prenom' => $line[2]);

                                if ($grp = $this->studentMod->isStudentInGroupsOfSemesters($student['numEtudiant'], $semestreDuringSamePeriod)) {

                                    $refus[] = array('student' => $student, 'fromGroup' => $grp, 'toGroup' => $this->adminMod->getGroupDetails($groupId));
                                } else {

                                    if (($key = array_search($student['numEtudiant'], $groupStudentIds)) !== FALSE) {
                                        $conserve++;

                                        unset($groupStudentIds[$key]);
                                        $alreadyAddedIds[$student['numEtudiant']] = $groupId;


                                    } else {
                                        if (isset($alreadyAddedIds[$student['numEtudiant']])) {
                                            $refus[] = array('student' => $student, 'fromGroup' => $this->adminMod->getGroupDetails($alreadyAddedIds[$student['numEtudiant']]), 'toGroup' => $this->adminMod->getGroupDetails($groupId));
                                        } else {
                                            $ajout++;
                                            $alreadyAddedIds[$student['numEtudiant']] = $groupId;
                                            $this->studentMod->addToGroupe($student['numEtudiant'], $groupId);
                                        }
                                    }

                                }
                            }
                        }
                    }
                    //TODO duplicate a reflechir cmment faire mieux car deux fois le meme code
                    if ($groupId != 0) {
                        foreach ($groupStudentIds as $studentId) {
                            $this->studentMod->deleteRelationGroupStudent($groupId, $studentId);
                            $delete++;
                        }
                    }

                    if (count($refus)) {
                        $msg = 'Erreur d\'import pour les etudiants:';
                        foreach ($refus as $refu) {

                            $msg .= $refu['student']['nom']
                                . ' ' . $refu['student']['prenom']
                                . ' de ' . $refu['fromGroup']->nomGroupe . $refu['fromGroup']->type
                                . ' a ' . $refu['toGroup']->nomGroupe . $refu['toGroup']->type . '<br>';
                        }
                        addPageNotification($msg, 'danger');

                    } else {
                        addPageNotification(
                            'Conservé : ' . $conserve
                            . ', Supprimé : ' . $delete
                            . ', Ajouté : ' . $ajout
                            . ', Total : ' . ($conserve + $delete + $ajout));
                    }

                    redirect('Administration/Semestre/' . $idSemestre);
                } else {
                    addPageNotification('Semestre non éditable', 'danger');
                }
            } else {
                addPageNotification('Format du fichier incorrect', 'danger');
            }
        } else {
            addPageNotification('Aucun fichier selectionné', 'danger');
        }
        redirect('Administration/Semestre/' . $idRedirect);
    }

}