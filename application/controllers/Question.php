<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller {

    public function student_index($page = 1) {
        $this->load->model('Students');

        //Number of questions you want per page
        $nbQuestionsPerPage = 15;
        //Size limit of the pagination
        $limitPagination = 9;
        //Pagination will be shifted from this number
        $changePaginationNumber = ceil($limitPagination / 2);

        $nbQuestions = $this->Students->countQuestions($_SESSION['id']);
        $nbPages = ceil($nbQuestions / $nbQuestionsPerPage);

        // Get questions and answers
        $unsortedQuestions = $this->Students->getQuestionsPerPage($_SESSION['id'], $page, $nbQuestionsPerPage);
        $unsortedAnswers = $this->Students->getAnswers($_SESSION['id']);
        $teachers = $this->Students->getTeachers($_SESSION['id']);

        if (!$unsortedQuestions) {
            redirect('Question');
        }

        $indexPagination = 1;
        if ($page > $changePaginationNumber) {
            if ($page <= $nbPages - $changePaginationNumber) {
                $indexPagination = 1 + ($page - $changePaginationNumber);
            } else {
                if ($page > $limitPagination) {
                    $indexPagination = 1 + ($nbPages - $limitPagination);
                }
            }
        }

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
            $questionsIds[] = $question->idQuestion;
        }

        foreach ($unsortedAnswers as $answer) {
            if (in_array($answer->idQuestion, $questionsIds)) {
                $questions[$answer->idQuestion]->answers[] = $answer;
            }
        }

        $this->data = array(
            'questions' => $questions,
            'teachers' => $teachers,
            'nbPages' => $nbPages,
            'currentPage' => $page,
            'indexPagination' => $indexPagination,
            'limitPagination' => $limitPagination
        );
        $this->show('Questions / Réponses');
    }

    public function teacher_index() {
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
