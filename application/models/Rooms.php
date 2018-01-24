<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends CI_Model
{

    /**
     * Get information about a room.
     *
     * @param int $roomId - The id of the timetable
     * @return int|bool FALSE if there's no resource.
     */
    public function get($roomId)
    {
        $res = $this->db
            ->from('RoomTimetable')
            ->where('idTimetable', $roomId)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Get all rooms' timetables.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db
            ->order_by('roomName')
            ->get('RoomTimetable')
            ->result();
    }

}
