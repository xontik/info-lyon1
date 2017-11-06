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

        $this->load->helper('csv_helper');

        $semester = $this->Semesters->get($semesterId);
        $students = $this->Semesters->getStudents($semesterId);

        header('Content-Type: text/csv');
        header('Content-Encoding: UTF-8');
        header('Content-disposition: attachment; filename=' . $semester->schoolYear . '-' . $semester->courseType . '.csv');



        $csv = array();
        $csv[] = array( 'IUT' => true,
                        'SEMESTRE' => $semester->idSemester,
                        'editable' => false);
        $csv[] = array( 'Type du semestre' => $semester->courseType,
                        'Annee scolaire' => $semester->schoolYear . '-' . ($semester->schoolYear + 1),
                        'editable' => false);
        $lastGroup = 0;
        foreach ($students as $student) {

            if ($lastGroup != $student->idGroup) {
                $lastGroup = $student->idGroup;
                $csv[] = array('newline' => 4);
                $csv[] = array('GROUPE' => $student->idGroup,
                                'Nom du groupe' => $student->groupName,
                                'editable' => false);

            }
            $csv[] = array( $student->idStudent,
                            $student->surname,
                            $student->name);

        }

        echo arrayToCsv($csv);


    }

    public function importCSVSemester($idRedirect)
    {
        $this->load->model('Semesters');
        $this->load->model('Groups');
        $this->load->helper('csv_helper');

        if (isset($_FILES['import']) && $_FILES['import']['size'] > 0) {

            if (isCSVFile($_FILES['import']['name'])) {

                $csv = csvToArray($_FILES['import']['tmp_name']);
                // echo '<pre>';
                // print_r($csv);
                // echo '</pre>';
                $idSemester = $csv[0][3];
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

                    redirect('Administration/Semester/' . $idSemester);
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
