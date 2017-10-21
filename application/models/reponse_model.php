<?php

class reponse_model extends CI_Model {

    public function getAnswers($idQuestion) {
        return $this->db->select('*')
                        ->from('Réponse')
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

        $this->db->insert('Réponse', $data);
    }

}
