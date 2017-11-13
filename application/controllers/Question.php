<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller {

    public function student_index($page = 1)
    {
        $page = (int) htmlspecialchars($page);

        if ($page === 0) {
            redirect('Question');
        }

        $this->load->model('Students');

        //Number of questions you want per page
        $nbQuestionsPerPage = 15;
        $nbQuestions = $this->Students->countQuestions($_SESSION['id']);
        $nbPages = ceil($nbQuestions / $nbQuestionsPerPage);
        if ($page > $nbPages) {
            redirect('Question');
        }

        $this->load->model('Questions');

        //Size limit of the pagination
        $limitPagination = 5;

        //Pagination will be shifted from this number
        $changePaginationNumber = ceil($limitPagination / 2);

        $unsortedQuestions = $this->Students->getQuestionsPerPage($_SESSION['id'], $page, $nbQuestionsPerPage);
        $unsortedAnswers = $this->Questions->getAllAnswers($unsortedQuestions);
        $teachers = $this->Students->getTeachers($_SESSION['id']);

        $indexPagination = 1;
        if ($page > $changePaginationNumber && $nbPages > $limitPagination) {
            if ($page <= $nbPages - $changePaginationNumber) {
                $indexPagination = 1 + $page - $changePaginationNumber;
            } else {
                $indexPagination = 1 + $nbPages - $limitPagination;
            }
        }

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
        }

        foreach ($unsortedAnswers as $answer) {
            $questions[$answer->idQuestion]->answers[] = $answer;
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

    public function teacher_index($page = 1) {
        $page = (int) htmlspecialchars($page);

        if ($page === 0) {
            redirect('Question');
        }

        $this->load->model('Teachers');

        //Number of questions you want per page
        $nbQuestionsPerPage = 15;
        $nbQuestions = $this->Teachers->countQuestions($_SESSION['id']);
        $nbPages = ceil($nbQuestions / $nbQuestionsPerPage);
        if ($page > $nbPages) {
            redirect('Question');
        }

        $this->load->model('Questions');

        //Size limit of the pagination
        $limitPagination = 5;

        //Pagination will be shifted from this number
        $changePaginationNumber = ceil($limitPagination / 2);

        $unsortedQuestions = $this->Teachers->getQuestionsPerPage($_SESSION['id'], $page, $nbQuestionsPerPage);
        $unsortedAnswers = $this->Questions->getAllAnswers($unsortedQuestions);

        $indexPagination = 1;
        if ($page > $changePaginationNumber && $nbPages > $limitPagination) {
            if ($page <= $nbPages - $changePaginationNumber) {
                $indexPagination = 1 + $page - $changePaginationNumber;
            } else {
                $indexPagination = 1 + $nbPages - $limitPagination;
            }
        }

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
        }

        foreach ($unsortedAnswers as $answer) {
            $questions[$answer->idQuestion]->answers[] = $answer;
        }

        $this->data = array(
            'questions' => $questions,
            'nbPages' => $nbPages,
            'currentPage' => $page,
            'indexPagination' => $indexPagination,
            'limitPagination' => $limitPagination
        );
        $this->show('Questions / Réponses');
    }

}
