<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller
{

    public function student_index()
    {
        $this->load->model('Students');

        // Get questions and answers
        $unsortedQuestions = $this->Students->getQuestions($_SESSION['id']);
        $unsortedAnswers = $this->Students->getAnswers($_SESSION['id']);
        $teachers = $this->Students->getTeachers($_SESSION['id']);

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
        }

        foreach ($unsortedAnswers as $answer) {
            $questions[$answer->idQuestion]->answers[] = $answer;
        }


        $this->data = array(
            'questions' => $questions,
            'teachers' => $teachers
        );
        $this->show('Questions / Réponses');
    }

    public function teacher_index()
    {
        $this->load->model('Teachers');

        $unsortedQuestions = $this->Teachers->getQuestions($_SESSION['id']);
        $unsortedAnswers = $this->Teachers->getAnswers($_SESSION['id']);

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
        }

        foreach ($unsortedAnswers as $answer) {
            $questions[$answer->idQuestion]->answers[] = $answer;
        }

        $this->data = array(
            'questions' => $questions
        );
        $this->show('Questions / Réponses');
    }
}
