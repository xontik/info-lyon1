<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetables extends CI_Model
{

    /**
     * Checks if a resource exists.
     *
     * @param int $resource
     * @return bool
     */
    public function exists($resource)
    {
        return !is_null($this->db
            ->where('resource', $resource)
            ->get('Timetable')
            ->row()
        );
    }

    /**
     * Checks if someone has a timetable.
     *
     * @param mixed $who
     * @param string $type 'group', 'teacher' or 'room'
     * @return bool
     */
    public function hasTimetable($who, $type)
    {
        $data = array();

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

        return !is_null($this->db
            ->where($data)
            ->get('Timetable')
            ->row()
        );
    }

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
        $this->db->set('json', $json)
            ->where('resource', $resource)
            ->update('Timetable');
        return $this->db->affected_rows();

    }

    /**
     * Create a timetable, associated to an owner, or not
     *
     * @param int $resource
     * @param mixed $who
     * @param string $type 'group', 'teacher' or 'room'
     * @return int|bool The id inserted on success, FALSE if there was a problem
     */
    public function create($resource, $who = null, $type = 'group')
    {
        $data = array(
            'resource' => $resource,
            'json' => ''
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

        if ($this->db->insert('Timetable', $data)) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     * Update the resource of the owner.
     *
     * @param int $resource
     * @param mixed $who
     * @param string $type 'group', 'teacher' or 'room'
     * @return bool
     */
    public function update($resource, $who, $type)
    {
        $typeToField = array(
            'group' => 'idGroup',
            'teacher' => 'idTeacher',
            'room' => 'idTimetable'
        );

        $json = $this->getJSON($resource);
        if (is_null($json)) {
            $json = '';
        }

        $typeField = $typeToField[$type];
        $who = (int) $who;

        $this->db
            ->set('resource', $resource)
            ->set('json', $json)
            ->where($typeField, $who)
            ->update('Timetable');
        return $this->db->affected_rows();

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
