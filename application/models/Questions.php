<?php

class Questions extends CI_Model {

    /**
     * Get all answers to a question.
     *
     * @param int $questionId
     * @return array
     */
    public function getAnswers($questionId)
    {
        return $this->db
            ->from('Answer')
            ->where('idQuestion', $questionId)
            ->order_by('answerDate', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get answers of all questions.
     *
     * @return array
     */
    public function getAllAnswers($questions)
    {
        $questionsId = array();
        foreach ($questions as $question) {
            $questionsId[] = $question->idQuestion;
        }

        return $this->db
            ->select('idQuestion, idAnswer, Answer.content, teacher')
            ->from('Answer')
            ->join('Question', 'idQuestion')
            ->where_in('idQuestion', $questionsId)
            ->order_by('questionDate', 'DESC')
            ->order_by('answerDate', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Returns the answers of the questions.
     *
     * @param array $questions
     * @return array
     */
    public function getAnswersAll($questions)
    {
        $questionsId = array();
        foreach ($questions as $question) {
            $questionsId[] = $question->idQuestion;
        }

        return $this->db
            ->select('idQuestion, idAnswer, Answer.content, teacher')
            ->from('Answer')
            ->join('Question', 'idQuestion')
            ->where_in('idQuestion', $questionsId)
            ->order_by('questionDate', 'DESC')
            ->order_by('answerDate', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Creates a questions.
     *
     * @param string $title
     * @param string $content
     * @param int $teacherId
     * @param string $studentId
     * @return int|bool The id of the question, FALSE if there was an error.
     */
    public function ask($title, $content, $teacherId, $studentId)
    {
        $data = array(
            'title' => $title,
            'content' => $content,
            'idTeacher' => $teacherId,
            'idStudent' => $studentId
        );
        if ($this->db->insert('Question', $data)) {
            return (int) $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     * Creates an answer.
     *
     * @param int $questionId
     * @param string $content
     * @param bool $isTeacher
     */
    public function answer($questionId, $content, $isTeacher)
    {
        $data = array(
            'idQuestion' => $questionId,
            'content' => $content,
            'teacher' => $isTeacher
        );

        $this->db->insert('Answer', $data);
    }

}
