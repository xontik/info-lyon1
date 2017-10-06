<?php

class question_model extends CI_Model {
	
  public function getProfessorQuestions($idProfessor) {
	  return $this->db->select('*')
		->from('Questions')
		->where('idProfesseur', $idProfessor)
		->get()
		->result();
  }
  
}
  
  