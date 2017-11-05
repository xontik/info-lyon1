<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Administration extends CI_Controller
{
    /*
     * AJAX
     */
    public function get_teaching_units()
    {
        $this->load->model('Courses');

        $courseId = (int) htmlspecialchars($_POST['courseId']);

        $output = array(
            'in' => $this->Courses->getTeachingUnitsIn($courseId),
            'out' => $this->Courses->getTeachingUnitsOut($courseId)
        );

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function getSemesterCSV($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        $this->load->model('Semesters');

        $semester = $this->Semesters->get($semesterId);
        $students = $this->Semesters->getStudents($semesterId);

        header('Content-Type: text/csv');
        header('Content-Encoding: UTF-8');
        header('Content-disposition: attachment; filename=' . $semester->schoolYear . '-' . $semester->courseType . '.csv');


        echo 'SEMESTRE;' . $semester->idSemester . ';<--Donnees non modifiable;;;' . PHP_EOL;
        echo 'Type du semestre;' . $semester->courseType
            . ';Annee scolaire;' . $semester->schoolYear . '-' . ($semester->schoolYear + 1)
            . ';<--Donnees non modifiable;' . PHP_EOL;

        $lastGroup = 0;
        foreach ($students as $student) {

            if ($lastGroup != $student->idGroup) {
                $lastGroup = $student->idGroup;
                echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
                echo 'GROUPE;' . $student->idGroup
                    . ';Nom du groupe;' . $student->groupName
                    . ';<--Donnees non modifiable;' . PHP_EOL;
            }
            echo $student->idStudent
                . ';' . $student->surname
                . ';' . $student->name
                . ';;;' . PHP_EOL;
        }
    }

    public function importCSV($idRedirect)
    {
        $this->load->model('Semesters');
        $this->load->model('Groups');

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


                $idSemester = $csv[0][1];
                $alreadyAddedIds = array();
                $groupStudentIds = array();

                $refusals = array();

                $preserved = 0;
                $deleted = 0;
                $added = 0;


                if ($this->Semesters->isEditable($idSemester)) {
                    $concurrentSemesters = $this->Semesters->getConcurrent($idSemester);

                    $groupId = 0;
                    foreach ($csv as $line) {

                        if ($line[0] == 'GROUPE') {
                            if ($groupId != 0) {
                                foreach ($groupStudentIds as $studentId) {
                                    $this->Groups->removeStudent($studentId, $groupId);
                                    $deleted++;
                                }
                            }

                            $groupId = $line[1];

                            if (!$this->Semesters->hasGroup($groupId, $idSemester)) {
                                $groupId = 0;
                            }

                            $groupStudentIds = array_map(function($element) {
                                return $element->idStudent;
                            }, $this->Groups->getStudents($groupId));

                        } else if ($groupId != 0) {
                            $student = array(
                                'idStudent' => $line[0],
                                'surname' => $line[1],
                                'name' => $line[2]
                            );

                            if ($group = $this->Semesters->anyHasStudent($student['idStudent'], $concurrentSemesters)) {

                                $refusals[] = array(
                                    'student' => $student,
                                    'fromGroup' => $group,
                                    'toGroup' => $this->Groups->get($groupId)
                                );
                            } else {

                                if (($key = array_search($student['idStudent'], $groupStudentIds)) !== FALSE) {
                                    $preserved++;

                                    unset($groupStudentIds[$key]);
                                    $alreadyAddedIds[$student['idStudent']] = $groupId;

                                } else {
                                    if (isset($alreadyAddedIds[$student['idStudent']])) {
                                        $refusals[] = array(
                                            'student' => $student,
                                            'fromGroup' => $this->Groups->get($alreadyAddedIds[$student['idStudent']]),
                                            'toGroup' => $this->Groups->get($groupId));
                                    } else {
                                        $added++;
                                        $alreadyAddedIds[$student['idStudent']] = $groupId;
                                        $this->Groups->addStudent($student['idStudent'], $groupId);
                                    }
                                }
                            }
                        }
                    }

                    if ($groupId != 0) {
                        foreach ($groupStudentIds as $studentId) {
                            $this->Groups->removeStudent($studentId, $groupId);
                            $deleted++;
                        }
                    }

                    if (!empty($refusals)) {
                        $msg = 'Erreur d\'import pour les etudiants suivants :<br>';
                        foreach ($refusals as $refusal) {

                            $msg .= $refusal['student']['surname']
                                . ' ' . $refusal['student']['name']
                                . ' de ' . $refusal['fromGroup']->groupName . $refusal['fromGroup']->courseType
                                . ' a ' . $refusal['toGroup']->groupName . $refusal['toGroup']->courseType . '<br>';
                        }
                        addPageNotification($msg, 'danger');

                    } else {
                        addPageNotification(
                            'Conservé : ' . $preserved . '<br>'
                            . 'Supprimé : ' . $deleted . '<br>'
                            . 'Ajouté : ' . $added . '<br>'
                            . 'Total : ' . ($preserved + $deleted + $added));
                    }

                    redirect('Administrations/Semester/' . $idSemester);
                } else {
                    addPageNotification('Semester non éditable', 'danger');
                }
            } else {
                addPageNotification('Format du fichier incorrect', 'danger');
            }
        } else {
            addPageNotification('Aucun fichier selectionné', 'danger');
        }
        redirect('Administration/Semester/' . $idRedirect);
    }

}
