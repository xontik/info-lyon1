<?php

class question_model extends CI_Model {

    public function getProfessorQuestions($idProfessor) {
        return $this->db->select('*')
                        ->from('Questions')
                        ->where('idProfesseur', $idProfessor)
                        ->get()
                        ->result();
    }

    public function getStudentQuestions($numEtudiant){
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

        $this->db->insert('Questions', $data);
    }
}
