<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetables extends CI_Model
{

    /**
     * Get the JSON of a timetable.
     *
     * @param int $resource
     * @return bool|string FALSE if the resource doesn't exist
     */
    public function getJSON($resource)
    {
        $res = $this->db
            ->where('resource', $resource)
            ->get('Timetable')
            ->row();

        if (empty($res)) {
            return FALSE;
        }
        return $res->json;
    }

    /**
     * Modifies the JSON of a resource.
     *
     * @param int $resource
     * @param string $json
     * @return bool
     */
    public function setJSON($resource, $json)
    {
        return $this->db->set('json', $json)
            ->where('resource', $resource)
            ->update('Timetable');
    }

    /**
     * Update the association between ressource and owner.
     *
     * @param int $resource
     * @param int $who The id of the owner
     * @param string $type 'group', 'teacher' or 'room'
     * @return bool
     */
    public function setOwner($resource, $who, $type)
    {
        $data = array(
            'idGroup' => null,
            'idTeacher' => null,
            'roomName' => null
        );

        switch($type) {
            case 'group':
                $data['idGroup'] = $who;
                break;
            case 'teacher':
                $data['idTeacher'] = $who;
                break;
            case 'room':
                $data['roomName'] = $who;
                break;
            default:
                trigger_error('Type is not valid');
                return false;
        }

        return $this->db
            ->set($data)
            ->where('resource', $resource)
            ->update('Timetable');
    }

    /**
     * Create a timetable, associated to an owner, or not
     *
     * @param int $resource
     * @param string $json
     * @param int $who The id of the owner (optionnal)
     * @param string $type 'group', 'teacher' or 'room'
     * @return int|bool The id inserted on success, FALSE if there was a problem
     */
    public function create($resource, $json, $who = null, $type = 'group')
    {
        $data = array(
            'resource' => $resource,
            'json' => $json
        );

        if ($who !== null) {
            switch($type) {
                case 'group':
                    $data['idGroup'] = $who;
                    break;
                case 'teacher':
                    $data['idTeacher'] = $who;
                    break;
                case 'room':
                    $data['roomName'] = $who;
                    break;
                default:
                    trigger_error('Type is not valid');
                    return false;
            }
        }

        if($this->db->insert('Timetable', $data)) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     * Deletes a timetable.
     *
     * @param $resourceId
     * @return bool
     */
    public function delete($resourceId)
    {
        return $this->db->delete('Timetable', array('resource' => $resourceId));
    }

}
