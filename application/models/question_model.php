<?php

class question_model extends CI_Model {

    public function getProfessorQuestions($idProfessor) {
        return $this->db->select('*')
            ->from('Questions')
            ->where('idProfesseur', $idProfessor)
            ->get()
            ->result();
    }

    public function getStudentQuestions($numEtudiant) {
        return $this->db->select('*')
            ->from('Questions')
            ->where('numEtudiant', $numEtudiant)
            ->get()
            ->result();
    }

    public function ask($titre, $texte, $idProfesseur, $numEtudiant) {
        $data = array(
            'titre' => $titre,
            'texte' => $texte,
            'idProfesseur' => $idProfesseur,
            'numEtudiant' => $numEtudiant
        );
        return $this->db->insert('Questions', $data);
    }

    public function getAnswers($idQuestion) {
        return $this->db->select('*')
            ->from('Reponses')
            ->where('idQuestion', $idQuestion)
            ->order_by('dateReponse', 'asc')
            ->get()
            ->result();
    }

    public function answer($idQuestion, $texte, $isProf) {
        $data = array(
            'idQuestion' => $idQuestion,
            'texte' => $texte,
            'prof' => $isProf
        );

        $this->db->insert('Reponses', $data);
    }

}
