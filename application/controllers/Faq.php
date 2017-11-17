<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends TM_Controller
{
    
    public function student_index()
    {
        $this->load->model('Students');
        
        $this->setData(array(
            'css' => 'Common/question'
        ));
        
        $this->load->helper('tabs');
        
        $tabs['Questions'] = createTab('Questions', 'Question');
        $tabs['Faq'] = createTab('Faq', 'Faq', true);
        
        $unsortedQuestions = $this->Students->getPublicQuestionsPerPage($_SESSION['id']);
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

        $this->show('FAQ');
    }

}
