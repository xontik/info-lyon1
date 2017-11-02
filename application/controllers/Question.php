<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller
{

    public function student_index()
    {
        $this->load->model('question_model', 'questionsMod');
        $this->load->model('students_model', 'studentsMod');
        $this->load->model('teacher_model', 'teacherMod');

        // Get questions and answers
        $questions = $this->questionsMod->getStudentQuestions($_SESSION['id']);
        $answers = array();

        foreach($questions as $question) {
            $answers[$question->idQuestion] = $this->questionsMod->getAnswers($question->idQuestion);
        }

        // Get teachers
        $unsortedTeachers = $this->studentsMod->getProfesseursByStudent($_SESSION['id']);
        $teachers = array();

        foreach($unsortedTeachers as $teacher) {
            $teachers[$teacher->idProfesseur] = $teacher;
        }

        $this->data = array(
            'questions' => $questions,
            'answers' => $answers,
            'teachers' => $teachers
        );
        $this->show('Questions / Réponses');
    }

    public function teacher_index()
    {
        $this->load->model('students_model', 'studentMod');
        $this->load->model('question_model', 'questionsMod');

        $questions = $this->questionsMod->getProfessorQuestions($_SESSION['id']);

        $students = array();
        $answers = array();

        foreach ($questions as $question) {
            $students[$question->numEtudiant] = $this->studentMod->getStudent($question->numEtudiant);
            $answers[$question->idQuestion] = $this->questionsMod->getAnswers($question->idQuestion);
        }

        $this->data = array(
            'questions' => $questions,
            'students' => $students,
            'answers' => $answers
        );
        $this->show('Questions / Réponses');
    }
}
