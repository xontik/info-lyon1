<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends TM_Controller
{

    public function student_index($page = 1, $questionId = 0)
    {
        $page = (int) htmlspecialchars($page);
        $questionId = (int) htmlspecialchars($questionId);

        $search = isset($_GET['s']) ? htmlspecialchars($_GET['s']) : '';

        if ($page <= 0) {
            redirect('Question');
        }

        $this->load->model('Students');
        $this->load->config('Question');
        
        $this->load->helper('tabs');
        
        $tabs[] = createTab('Questions', 'Question', true);
        $tabs[] = createTab('FAQ', 'Question/public');

        $unsortedQuestions = $this->Students->getQuestionsPerPage($_SESSION['id'],
            $page, $this->config->item('questionByPage'), $search);

        $nbQuestions = $this->Students->countQuestions($_SESSION['id'], $search);

        if (!$unsortedQuestions && $search !== '') {
            addPageNotification('Aucun résultat trouvé', 'warning');
            redirect('Question');
        }

        $questionList = $this->_computeQuestionList(
            $page, $unsortedQuestions, $questionId,
            $nbQuestions, false, $search
        );

        $teachers = $this->Students->getTeachers($_SESSION['id']);

        $this->data = array(
            'tabs' => $tabs,
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

        $search = isset($_GET['s']) ? htmlspecialchars($_GET['s']) : '';

        if ($page <= 0) {
            redirect('Question');
        }

        $this->load->model('Teachers');
        $this->load->config('Question');

        $this->load->helper('tabs');

        $tabs[] = createTab('Questions', 'Question', true);
        $tabs[] = createTab('FAQ', 'Question/public');

        $unsortedQuestions = $this->Teachers->getQuestionsPerPage($_SESSION['id'],
            $page, $this->config->item('questionByPage'), $search);

        $nbQuestions = $this->Teachers->countQuestions($_SESSION['id'], $search);

        if (!$unsortedQuestions && $search !== '') {
            addPageNotification('Aucun résultat trouvé', 'warning');
            redirect('Question');
        }

        $questionList = $this->_computeQuestionList(
            $page, $unsortedQuestions, $questionId,
            $nbQuestions, true, $search
        );

        $this->data = array(
            'tabs' => $tabs,
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

    private function _public($unsortedQuestions)
    {
        $this->setData(array(
            'css' => 'Common/question'
        ));

        $this->load->helper('tabs');

        $tabs[] = createTab('Questions', 'Question');
        $tabs[] = createTab('FAQ', 'Question/public', true);

        // Answers
        $questions = array();
        if (!empty($unsortedQuestions)) {
            $this->load->model('Questions');
            $unsortedAnswers = $this->Questions->getAllAnswers($unsortedQuestions);

            foreach ($unsortedQuestions as $question) {
                $questions[$question->idQuestion] = $question;
            }

            foreach ($unsortedAnswers as $answer) {
                $questions[$answer->idQuestion]->answers[] = $answer;
            }
        }

        $this->data = array(
            'tabs' => $tabs,
            'questions' => $questions
        );

        $this->setData('view', 'Common/question_public');
        $this->show('FAQ');
    }

    public function student_public() {
        $this->load->model('Students');

        $unsortedQuestions = $this->Students->getPublicQuestionsPerPage($_SESSION['id']);

        $this->_public($unsortedQuestions);
    }

    public function teacher_public() {
        $this->load->model('Teachers');

        $unsortedQuestions = $this->Teachers->getPublicQuestionsPerPage($_SESSION['id']);

        $this->_public($unsortedQuestions);
    }

    /**
     * Get the question-list view.
     *
     * @param int       $page
     * @param array     $unsortedQuestions
     * @param int       $activeQuestion
     * @param int       $userQuestionCount
     * @param boolean   $teacher
     * @param string    $search
     * @return string
     */
    private function _computeQuestionList($page, $unsortedQuestions, $activeQuestion,
                                          $userQuestionCount, $teacher, $search)
    {
        $this->load->model('Questions');
        $this->setData(array(
            'css' => 'Common/question',
            'js' => 'Common/question'
        ));

        // Pagination
        $questionsPerPage = $this->config->item('questionByPage');
        $paginationMaxCount = $this->config->item('paginationLimit');

        // Pagination will be shifted from this number
        $changePaginationNumber = ceil($paginationMaxCount / 2);

        $nbPages = ceil($userQuestionCount / $questionsPerPage);
        if (!empty($unsortedQuestions) && $page > $nbPages) {
            redirect('Question');
        }
        
        
        $beginPagination = 1;
        if ($page > $changePaginationNumber && $nbPages > $paginationMaxCount) {
            if ($page <= $nbPages - $changePaginationNumber) {
                $beginPagination = 1 + $page - $changePaginationNumber;
            } else {
                $beginPagination = 1 + $nbPages - $paginationMaxCount;
            }
        }

        // Answers
        $questions = array();
        if (!empty($unsortedQuestions)) {
            $unsortedAnswers = $this->Questions->getAllAnswers($unsortedQuestions);

            foreach ($unsortedQuestions as $question) {
                $questions[$question->idQuestion] = $question;
            }

            foreach ($unsortedAnswers as $answer) {
                $questions[$answer->idQuestion]->answers[] = $answer;
            }
        }
        
        return $this->load->view(
            'includes/question-list',
            array(
                'activeQuestion' => $activeQuestion,
                'teacher' => $teacher,
                'questions' => $questions,
                'search' => $search,
                'nbPages' => $nbPages,
                'currentPage' => $page,
                'indexPagination' => $beginPagination,
                'limitPagination' => $paginationMaxCount
            ),
            TRUE
        );
    }

}
