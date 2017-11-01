<?php

class Process_secretariat extends CI_Controller
{

    public function ajout_absence()
    {
        header('Content-Type: text/plain');

        if (!isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        if (!$this->_checkAbsenceData($data)) {
            echo 'wrong_data';
            return;
        }

        try {
            $this->db->insert('Absences', $data);
            $absenceId = $this->db->select_max('idAbsence')
                ->get('Absences')
                ->row()->idAbsence;
            echo 'success ' . $absenceId;
        } catch (PDOException $e) {
            echo 'exception : ' . $e->getMessage();
        }

    }

    private function _checkAbsenceData($data)
    {
        return !empty($data['numEtudiant'])
            && !empty($data['dateDebut'])
            && !empty($data['dateFin'])
            && $data['dateDebut'] !== $data['dateFin']
            && ($data['justifiee'] == 0 || $data['justifiee'] == 1);
    }

    public function modifier_absence()
    {
        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])
            || !isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $absenceId = htmlspecialchars($_POST['absenceId']);
        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        if (!$this->_checkAbsenceData($data)) {
            echo 'wrong_data';
            return;
        }

        try {
            $this->db->set($data)
                ->where('idAbsence', $absenceId)
                ->update('Absences', $data);
            echo 'success ' . $absenceId;
        } catch (Exception $e) {
            echo 'exception : ' . $e->getMessage();
        }
    }

    public function suppression_absence()
    {
        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])) {
            echo 'missing_data';
            return;
        }

        $absenceId = htmlspecialchars($_POST['absenceId']);

        try {
            $this->db->delete('Absences', array('idAbsence' => $absenceId));
            echo 'success';
        } catch (Exception $e) {
            echo 'exception: ' . $e->getMessage();
        }

    }

    public function getUEs()
    {
        $this->load->model('administration_model', 'adminMod');
        
        $UEsIn = $this->adminMod->getUEInParcours($_GET['idParcours']);
        $UEsOut = $this->adminMod->getUENotInParcours($_GET['idParcours']);
        
        $output = array(
            'in' => $UEsIn,
            'out' => $UEsOut
        );
        
        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function addUEtoParcours()
    {
        $this->load->model('administration_model', 'adminMod');
        $ids = array();
        if ($this->adminMod->isThisParcoursEditable($_GET['idParcours'])) {

            foreach ($_GET['idUEs'] as $idUE) {

                if ($this->adminMod->addUEtoParcours($_GET['idParcours'], $idUE)) {
                    $ids[] = $idUE;
                }
            }

        }
        header('Content-Type: application/json');

        echo json_encode($ids);
    }

    public function removeUEtoParcours()
    {
        $this->load->model('administration_model', 'adminMod');
        $ids = array();
        if ($this->adminMod->isThisParcoursEditable($_GET['idParcours'])) {

            foreach ($_GET['idUEs'] as $idUE) {
                if ($this->adminMod->removeUEtoParcours($_GET['idParcours'], $idUE)) {
                    $ids[] = $idUE;
                }
            }
        }
        header('Content-Type: application/json');

        echo json_encode($ids);

    }

    public function addParcours()
    {
        $this->load->model('administration_model', 'adminMod');

        if (isset($_POST['year']) && isset($_POST['type'])) {
            if (is_numeric($_POST['year']) && strlen($_POST['type']) == 2) {
                if ($this->adminMod->addParcours($_POST['year'], $_POST['type'])) {
                    addPageNotification('Parcours créé !');
                } else {
                    addPageNotification('Erreur d\'ajout bdd !');
                }
            } else {
                addPageNotification('Données entrées corrompues !');
            }
        } else {
            addPageNotification('Données manquantes !');
        }
        redirect('Secretariat/administration');
    }

    public function deleteParcours()
    {
        $this->load->model('administration_model', 'adminMod');
        if (isset($_POST['parcours'])) {
            if ($this->adminMod->isThisParcoursEditable($_POST['parcours'])) {
                if ($this->adminMod->deleteCascadeParcours($_POST['parcours'])) {
                    addPageNotification('Parcours supprimé !');
                } else {
                    addPageNotification('Erreur de suppression bdd !');
                }
            } else {
                addPageNotification('Ce parcours ne peut etre supprimé !');
            }
        } else {
            addPageNotification('Données manquantes !');
        }
        redirect('Secretariat/administration');
    }

    public function addSemestre()
    {
        $this->load->model('semester_model', 'semMod');
        $this->load->model('administration_model', 'adminMod');

        if (isset($_POST['anneeScolaire']) && isset($_POST['parcours'])) {
            if ($this->adminMod->isThisParcoursExist($_POST['parcours'])) {
                $semester = (object)array('differe' => isset($_POST['chkDiffere']) ? 1 : 0, 'anneeScolaire' => $_POST['anneeScolaire'], 'idParcours' => $_POST['parcours'], 'type' => $this->adminMod->getParcoursType($_POST['parcours']));
                $now = new DateTime();
                $dateStart = $this->semMod->getSemesterObjectPeriod($semester)->getBeginDate();
                if ($now < $dateStart) {
                    if ($this->semMod->addSemester($semester->idParcours, $semester->differe, $semester->anneeScolaire)) {
                        addPageNotification('Semestre créé');
                    } else {
                        addPageNotification('Impossible d\'ajouter car ce semestre existe deja !');
                    }
                } else {
                    addPageNotification('Impossible de creer ce semestre car il aurait deja du commencer !');
                }
            } else {
                addPageNotification('Parcours inconnu');
            }
        } else {
            addPageNotification('Données manquantes !');
        }
        redirect('Secretariat/administration');

    }

    public function deleteSemestre($id)
    {
        $this->load->model('semester_model', 'semMod');
        if ($this->semMod->isSemesterDeletable($id)) {
            if ($this->semMod->deleteSemestre($id)) {
                addPageNotification('Semestre supprimé !');
            } else {
                addPageNotification('Erreur de suppression bdd !');
            }
        } else {
            addPageNotification('Ce semestre ne peut etre supprimé !');
        }
        redirect('Secretariat/administration');

    }

    public function getCSVGroupeSemestre($id)
    {
        $this->load->model('Students_model', 'studentMod');
        $this->load->model('Semester_model', 'semMod');

        $semestre = $this->semMod->getSemesterById($id);

        $groups = $this->studentMod->getStudentsBySemestre($id);
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
        //

    }

    public function addGroupe($idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');

        if (isset($_POST['nomGroupe'])) {
            //TODO add preg_match
            if ($this->semMod->isSemesterEditable($idSemestre)) {
                if ($this->adminMod->addGroupe($idSemestre, $_POST['nomGroupe'])) {
                    addPageNotification('Groupe ' . $_POST['nomGroupe'] . ' ajouté!');
                } else {
                    addPageNotification('Erreur de création de groupe !');
                }
            } else {
                addPageNotification('Ce semestre ne peut etre modifié !');
            }
        } else {
            addPageNotification('Données manquantes !');
        }
        redirect('Secretariat/gestionSemestre/' . $idSemestre);

    }

    public function deleteGroupe($idGroupe, $idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');


        if ($this->adminMod->isGroupeEditable($idGroupe)) {
            if ($this->adminMod->deleteGroupe($idGroupe)) {
                addPageNotification('Groupe supprimé !');
            } else {
                addPageNotification('Erreur de suppression du groupe !');
            }
        } else {
            addPageNotification('Ce semestre ne peut etre modifié !');
        }
        redirect('Secretariat/gestionSemestre/' . $idSemestre);

    }

    public function importCSV($idRedirect)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');
        $this->load->model('students_model', 'studentMod');

        if (isset($_FILES['import']) && $_FILES['import']['size'] > 0) {
            $format = strtolower(array_slice(
                explode('.', $_FILES['import']['name']), -1)[0]);
            if ($format === 'csv') {
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


                if ($this->semMod->isSemesterEditable($idSemestre)) {
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
                    //TODO faire notif correct quand merge avec enzo
                    if (count($refus)) {
                        $msg = 'Erreur d\'import pour les etudiants:';
                        foreach ($refus as $refu) {

                            $msg .= $refu['student']['nom'] . ' ' . $refu['student']['prenom'] . ' de ' . $refu['fromGroup']->nomGroupe . $refu['fromGroup']->type . ' a ' . $refu['toGroup']->nomGroupe . $refu['toGroup']->type . ' <br>';
                        }
                        addPageNotification($msg, 'danger');

                    } else {
                        addPageNotification('Conserve : ' . $conserve . ' ' . 'Delete :' . $delete . ' ' . 'Ajout : ' . $ajout . ' ' . 'Total : ' . ($conserve + $delete + $ajout), 'danger');
                    }

                    redirect('Secretariat/gestionSemestre/' . $idSemestre);
                    exit(0);


                } else {
                    addPageNotification('Identifiant semestre incorrect', 'danger');
                }
            } else {
                addPageNotification('Fichier format incorrect', 'danger');
            }
        } else {
            addPageNotification('Aucun fichier reçu', 'danger');
        }
        redirect('Secretariat/gestionSemestre/' . $idRedirect);


    }

    public function addStudentGroup($idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');
        $this->load->model('students_model', 'studentMod');

        if (isset($_POST['submit']) && isset($_POST['grp' . $_POST['submit']])) {
            $numEtudiant = $_POST['grp' . $_POST['submit']];
            $idGroupe = $_POST['submit'];
            $semestreDuringSamePeriod = $this->semMod->getSemesterIdsSamePeriod($idSemestre);
            $groupStudentIds = $this->studentMod->getIdsFromGroup($idGroupe);

            if ($this->adminMod->isGroupeEditable($idGroupe)) {
                if ($grp = $this->studentMod->isStudentInGroupsOfSemesters($numEtudiant, $semestreDuringSamePeriod)) {
                    addPageNotification('Impossible d\'ajouter cette etudiant car il est deja en ' . $grp->nomGroupe . $grp->type);
                } else {
                    $otherGroups = $this->semMod->getOtherGroups($idGroupe);
                    $delete = false;
                    foreach ($otherGroups as $idGrp) {
                        if ($this->studentMod->isStudentInGroup($numEtudiant, $idGrp)) {
                            $this->studentMod->deleteRelationGroupStudent($idGrp, $numEtudiant);
                            $delete = true;
                        }
                    }
                    if ($delete) {
                        addPageNotification('Etudiant déplacé !');
                    } else {
                        addPageNotification('Etudiant ajouté !');
                    }
                    $this->studentMod->addToGroupe($numEtudiant, $idGroupe);

                }
            } else {
                addPageNotification('Impossible d\'editer ce groupe');
            }
        } else {
            addPageNotification('Données manquantes');
        }

        redirect('Secretariat/gestionSemestre/' . $idSemestre);
    }

    public function deleteRelation($groupId, $numEtudiant, $idSemestre)
    {

        $this->load->model('administration_model', 'adminMod');
        $this->load->model('students_model', 'studentMod');

        if ($this->adminMod->isGroupeEditable($groupId)) {
            $this->studentMod->deleteRelationGroupStudent($groupId, $numEtudiant);
            addPageNotification('Etudiant supprimé !');
        } else {
            addPageNotification('Impossible d\'editer ce groupe');
        }
        redirect('Secretariat/gestionSemestre/' . $idSemestre);

    }

}
