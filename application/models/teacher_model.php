<?php

class teacher_model extends CI_Model {

    public function getProfInfo($idProf) {
        return $this->db->select('nom, prenom, mail')
                        ->from('professeurs')
                        ->where('idProfesseur', $idProf)
                        ->get()
                        ->row();
    }
}
    