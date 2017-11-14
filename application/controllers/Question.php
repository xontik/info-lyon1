<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller
{

    public function student_index($page = 1)
    {
        $page = (int) htmlspecialchars($page);

        if ($page === 0) {
            redirect('Question');
        }

        $this->load->model('Students');
        $this->load->config('Question');

        $nbQuestions = $this->Students->countQuestions($_SESSION['id']);
        $unsortedQuestions = $this->Students->getQuestionsPerPage($_SESSION['id'], $page, $this->config->item('questionByPage'));

        $questionList = $this->_computeQuestionList($page, $unsortedQuestions, $nbQuestions, false);

        $teachers = $this->Students->getTeachers($_SESSION['id']);

        $this->data = array(
            'teachers' => $teachers,
            'questionList' => $questionList
        );
        $this->show('Questions / Réponses');
    }

    public function teacher_index($page = 1)
    {
        $page = (int) htmlspecialchars($page);

        if ($page === 0) {
            redirect('Question');
        }

        $this->load->model('Teachers');
        $this->load->config('Question');
        
        $nbQuestions = $this->Teachers->countQuestions($_SESSION['id']);
        $unsortedQuestions = $this->Teachers->getQuestionsPerPage($_SESSION['id'], $page, $this->config->item('questionByPage'));
        
        $questionList = $this->_computeQuestionList($page, $unsortedQuestions, $nbQuestions, true);

        $this->data = array(
            'questionList' => $questionList
        );

        $this->show('Questions / Réponses');
    }

    private function _computeQuestionList($page, $unsortedQuestions, $nbQuestions, $choosePublic)
    {
        $this->load->model('Questions');
        
        $nbQuestionsPerPage = $this->config->item('questionByPage');
        $limitPagination = $this->config->item('paginationLimit');

        //Pagination will be shifted from this number
        $changePaginationNumber = ceil($limitPagination / 2);

        $nbPages = ceil($nbQuestions / $nbQuestionsPerPage);
        if ($page > $nbPages) {
            redirect('Question');
        }

        $indexPagination = 1;
        if ($page > $changePaginationNumber && $nbPages > $limitPagination) {
            if ($page <= $nbPages - $changePaginationNumber) {
                $indexPagination = 1 + $page - $changePaginationNumber;
            } else {
                $indexPagination = 1 + $nbPages - $limitPagination;
            }
        }

        $unsortedAnswers = $this->Questions->getAllAnswers($unsortedQuestions);

        $questions = array();
        foreach ($unsortedQuestions as $question) {
            $questions[$question->idQuestion] = $question;
        }

        foreach ($unsortedAnswers as $answer) {
            $questions[$answer->idQuestion]->answers[] = $answer;
        }

        return $this->load->view(
            'includes/question-list',
            array(
                'choosePublic' => $choosePublic,
                'questions' => $questions,
                'nbPages' => $nbPages,
                'currentPage' => $page,
                'indexPagination' => $indexPagination,
                'limitPagination' => $limitPagination
            ),
            TRUE
        );
    }

}
