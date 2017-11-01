<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable_model extends CI_Model
{

    /**
     * Get the JSON of a timetable
     * @param int $resources The ADE resource
     * @return bool|string The JSON of the timetable, or FALSE if it doesn't exist
     */
    public function getTimetableJSON($resources)
    {
        $rset = $this->db->where('ressource', $resources)
            ->get('edt')
            ->row();
        if (!isset($rset)) {
            return FALSE;
        }
        return $rset->edt;
    }

    /**
     * Modifies the JSON of a resource.
     * @param int $resources The ADE resource
     * @param string $json The new JSON to be applied
     */
    public function setTimetableJSON($resources, $json)
    {
        $this->db->set('edt', $json)
            ->where('ressource', $resources)
            ->update('edt');
    }

    /**
     * Create a timetable, associated to an owner or not
     * @param int $resources The ADE resource
     * @param string $json The JSON of the timetable
     * @param int $who The id of the owner (optionnal)
     * @param bool $isGroup Whether the id belongs to a group or a teacher (optionnal)
     */
    public function createTimetable($resources, $json, $who = null, $isGroup = true) {
        $data = array(
            'ressource' => $resources,
            'edt' => $json
        );

        if ($who !== null) {
            if ($isGroup) {
                $data['idGroupe'] = $who;
            } else {
                $data['idProfesseur'] = $who;
            }
        }

        $this->db->insert('edt', $data);
    }

    /**
     * Update the association between ressource and owner
     * @param int $resources The ADE resource
     * @param int $who The id of the owner
     * @param bool $isGroup Whether the id belongs to a group or a teacher
     */
    public function updateResourcesOwner($resources, $who, $isGroup = true) {
        $data = array(
            'idGroupe' => null,
            'idProfesseur' => null
        );

        if ($isGroup)
            $data['idGroupe'] = $who;
        else
            $data['idProfesseur'] = $who;

        $this->db->set($data)->where('ressource', $resources);
        $this->db->update('edt');
    }

}
