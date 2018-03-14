<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absence extends TM_Controller
{
    public function student_index($semester = '')
    {
        if (!preg_match('/^S[1-4]$/', $semester)) {
            $semester = '';
        }

        $this->load->model('Students');
        $this->load->model('Semesters');

        $this->load->helper('tabs');

        // Loads the max semester type the student went to
        $maxSemester = (int) substr(
            $this->Students->getCurrentSemester($_SESSION['id'])->courseType,
            1
        );

        if ($semester !== '' && $semester > "S$maxSemester") {
            addPageNotification('Vous essayez d\'accéder à un semestre futur<br />
              Redirection vers votre semestre courant');
            $semester = '';
        }

        // Tabs content
        $tabs = array();
        for ($i = 1; $i <= $maxSemester; $i++) {
            $tabs["S$i"] = createTab("Semestre $i", "Absence/S$i");
        }

        $semesterId = $this->Semesters->getSemesterId($semester, $_SESSION['id']);
        
        if ($semesterId === FALSE) {
          addPageNotification('Nous n\'avons pas d\'informations sur ce semestre<br />
            Vous avez été redirigé vers votre semestre courant');
          redirect('Absence');
          return;
        }
        
        $semesterType = $this->Semesters->getType($semesterId);
        $tabs[$semesterType]->active = true;

        $absences = $this->Semesters->getStudentAbsence($_SESSION['id'], $semesterId);

        $this->data = array(
            'tabs' => $tabs,
            'absences' => $absences
        );

        $this->show('Absences');
    }

    public function teacher_index()
    {
        $this->load->model('Teachers');
        $this->load->helper('timetable');

        // Timetable
        $adeResource = $this->Teachers->getADEResource($_SESSION['id']);
        $students = null;
        $currEvent = null;

        if ($adeResource === FALSE) {
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array(
                    'date' => new DateTime(),
                    'timetable' => false,
                    'minTime' => '08:00',
                    'maxTime' => '18:00'
                ),
                TRUE
            );
        } else {
            $now = new DateTime();
            if (isset($_GET['h'])
                && preg_match('/^(([01]\d)|(2[0-3])):[0-5]\d$/', $_GET['h'])
            ) {
                $time = $_GET['h'];
            } else {
                $time = $now->format('H:i');
            }

            $result = getNextTimetable($adeResource, 'day');

            if ($now->format('Y-m-d') === $result['date']->format('Y-m-d')) {

                foreach ($result['timetable'] as $key => $event) {
                    $result['timetable'][$key]['link'] = 'Absence?h=' . $event['timeStart'];
                    if ($time >= $event['timeStart'] && $time < $event['timeEnd']) {
                        $currEvent = $event;
                    }
                }

                if ($currEvent) {
                    if (preg_match('/^G\d+S[1-4]$/', $currEvent['groups'])) {
                        $this->load->model('Groups');
                        $groupId = $this->Groups->getIdFromName($currEvent['groups']);
                        if ($groupId !== FALSE) {
                            $students = $this->Groups->getStudents($groupId);
                        }
                    } else if (preg_match('/^S[1-4]$/', $currEvent['groups'])) {
                        $this->load->model('Semesters');
                        $semesterId = $this->Semesters->getIdFromName($currEvent['groups']);
                        if ($semesterId !== FALSE) {
                            $students = $this->Semesters->getStudents($semesterId);
                        }
                    }

                    if (!empty($students)) {
                        $this->load->model('Absences');
                        $timeStart = DateTime::createFromFormat('H:i', $currEvent['timeStart']);

                        $absences = $this->Absences->getAtTime($timeStart, $students);
                        
                        foreach ($students as $oldkey => $student) {
                            $students[$student->idStudent] = $student;
                            unset($students[$oldkey]);
                        }

                        foreach ($absences as $absence) {
                            $students[$absence->idStudent]->absence = $absence;
                        }
                    }
                }
            }

            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                $result,
                TRUE
            );
        }

        $this->data['side-timetable'] = $sideTimetable;
        $this->data['students'] = $students;
        $this->data['lesson'] = $currEvent;

        $this->show('Absences');
    }

    public function secretariat_index()
    {
        $this->load->model('Absences');
        $this->load->model('Semesters');
        $this->load->model('Students');

        $this->load->helper('time');

        $period = $this->Semesters->getCurrentPeriod();

        if ($period !== FALSE) {

            $students = $this->Students->getAllOrganized();
            $unsortedAbsences = $this->Absences->getInPeriod($period);

            // Associate absence to the student
            $absences = array();
            foreach ($unsortedAbsences as $absence) {
                $absences[$absence->idStudent][] = $absence;
            }

            // Associate students absences to the day it happened
            $groups = array();
            $assoc = array();

            foreach ($students as $student) {

                if (!isset($assoc[$student->idStudent])) {
                    $student->absences = array(
                        'total' => 0,
                        'totalDays' => 0,
                        'justified' => 0
                    );

                    $assoc[$student->idStudent] = $student;

                    if (isset($groups[$student->groupName])) {
                        $groups[$student->groupName] += 1;
                    } else {
                        $groups[$student->groupName] = 1;
                    }
                }

                if (isset($absences[$student->idStudent])) {

                    foreach ($absences[$student->idStudent] as $absence) {
                        $index = $period->getDays(new DateTime($absence->beginDate));
                        $assoc[$student->idStudent]->absences[$index][] = $absence;

                        if ($absence->justified) {
                            $assoc[$student->idStudent]->absences['justified'] += 1;
                        }
                    }

                    $assoc[$student->idStudent]->absences['total'] =
                        count($absences[$student->idStudent]);
                    $assoc[$student->idStudent]->absences['totalDays'] =
                        count($assoc[$student->idStudent]->absences) - 3;
                }
            }

            $this->data = array(
                'loaded' => true,
                'absences' => $assoc,
                'groups' => $groups,
                'beginDate' => $period->getBeginDate(),
                'dayNumber' => $period->getDays(),
                'absenceTypes' => $this->Absences->getTypes()
            );
        }
        else {
            $this->data['loaded'] = false;
        }

        $this->show('Absences');
    }

}
