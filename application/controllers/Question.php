<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller
{

    public function student_index($page = 1, $questionId = 0)
    {
        $page = (int) htmlspecialchars($page);
        $questionId = (int) htmlspecialchars($questionId);

        if ($page <= 0) {
            redirect('Question');
        }

        $this->load->model('Students');
        $this->load->config('Question');

        $nbQuestions = $this->Students->countQuestions($_SESSION['id']);
        
        $questionList = $this->_computeQuestionList($page, $questionId, $unsortedQuestions, $nbQuestions, false);

        $teachers = $this->Students->getTeachers($_SESSION['id']);

        $this->data = array(
            'teachers' => $teachers,
            'questionList' => $questionList
        );

        $this->show('Questions / Réponses');
    }

    public function student_detail($questionId = 0)
    {
        $questionId = (int) htmlspecialchars($questionId);

        if ($questionId <= 0) {
            redirect('Question');
        }

        $this->load->model('Students');
        $this->load->config('Question');

        $page = $this->Students->getPage($questionId,
            $_SESSION['id'],
            $this->config->item('questionByPage')
        );

        $this->setData('view', 'Student/question');
        $this->student_index($page, $questionId);
    }

    public function teacher_index($page = 1, $questionId = 0)
    {
        $page = (int) htmlspecialchars($page);
        $questionId = (int) htmlspecialchars($questionId);

        if ($page <= 0) {
            redirect('Question');
        }

        $this->load->model('Teachers');
        $this->load->config('Question');
        
        if(isset($_POST['search']) || isset($_GET['search'])){
            if(isset($_POST['search']))
                $search = htmlspecialchars($_POST['search']);
            if(isset($_POST['search']))
                $search = htmlspecialchars($_POST['search']);
            $questions = $this->Teachers->getQuestionsPerPage($_SESSION['id'], $page, $this->config->item('questionByPage'), $search);
            if(!$questions){
                $unsortedQuestions = $this->Teachers->getQuestionsPerPage($_SESSION['id'], $page, $this->config->item('questionByPage'));
                $nbQuestions = $this->Teachers->countQuestions($_SESSION['id']);
                addPageNotification('Aucun résultat trouvé.', 'warning');
            }
            else{
                $unsortedQuestions = $questions;
                $nbQuestions = $this->Teachers->countQuestions($_SESSION['id'], $search);
            } 
        }
        else{
            $unsortedQuestions = $this->Teachers->getQuestionsPerPage($_SESSION['id'], $page, $this->config->item('questionByPage'));
            $nbQuestions = $this->Teachers->countQuestions($_SESSION['id']);
    
        }
                
        $questionList = $this->_computeQuestionList($page, $questionId, $unsortedQuestions, $nbQuestions, true ,$search = '');

        $this->data = array(
            'questionList' => $questionList
        );

        $this->show('Questions / Réponses');
    }

    public function teacher_detail($questionId = 0)
    {
        $questionId = (int) htmlspecialchars($questionId);

        if ($questionId <= 0) {
            redirect('Question');
        }

        $this->load->model('Teachers');
        $this->load->config('Question');

        $page = $this->Teachers->getPage($questionId,
            $_SESSION['id'],
            $this->config->item('questionByPage')
        );
        
        $this->setData('view', 'Teacher/question');
        $this->teacher_index($page, $questionId);
    }

    private function _computeQuestionList($page, $questionId, $unsortedQuestions, $nbQuestions, $choosePublic, $search)
    {
        $this->load->model('Questions');
        $this->setData('css', 'Common/question');
        
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
                'questionId' => $questionId,
                'choosePublic' => $choosePublic,
                'questions' => $questions,
                'nbPages' => $nbPages,
                'currentPage' => $page,
                'indexPagination' => $indexPagination,
                'limitPagination' => $limitPagination,
                'search' => $search
            ),
            TRUE
        );
    }

}
