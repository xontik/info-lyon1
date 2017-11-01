<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_model extends CI_Model
{

    public function getCalendarJSON($resources)
    {
        return $this->db->where('ressource', $resources)
            ->get('edt')
            ->row();
    }

    public function setCalendarJSON($resources, $json)
    {
        return $this->db->set('edt', $json)
            ->where('ressource', $resources)
            ->update();
    }

    public function createCalendar($resources, $json, $who = NULL, $isGroup = true) {
        $data = array(
            'ressource' => $resources,
            'edt' => $json
        );

        if (!is_null($who)) {
            if ($isGroup)
                $data['idGroupe'] = $who;
            else
                $data['idProfesseur'] = $who;
        }

        return $this->db->insert('edt', $data);
    }

    public function updateCalendarResourcesOwner($resources, $who, $isGroup = true) {
        $data = array(
            'idGroupe' => null,
            'idProfesseur' => null
        );

        $data[$isGroup ? 'idGroupe' : 'idProfesseur'] = $who;

        return $this->db->set($data)
            ->where('ressource', $resources)
            ->update('edt');
    }

}
