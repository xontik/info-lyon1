<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_model extends CI_Model
{

    public function getCalendarJSON($resources)
    {
        $query = 'SELECT * FROM edt WHERE ressource = ?';
        $result = $this->db->query($query, array($resources))->row();
        if (isset($result))
            return $result->edt;
    }

    public function setCalendarJSON($resources, $json)
    {
        $query = 'UPDATE edt SET edt = ? WHERE ressource = ?';
        return $this->db->simple_query($query, array($json, $resources));
    }

    public function createCalendar($resources, $json, $who = NULL, $isGroup = true) {
        $data = array(
            'ressource' => $resources,
            'edt' => $json
        );

        if ($who != NULL) {
            if ($isGroup)
                $data['idGroupe'] = $who;
            else
                $data['idProfesseur'] = $who;
        }

        return $this->db->insert('edt', $data);
    }

}
