<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends CI_Model
{

    /**
     * Get the resource for timetables.
     *
     * @param $roomName
     * @return int|bool FALSE if there's no resource.
     */
    public function getADEResource($roomName)
    {
        $res = $this->db
            ->select('resource')
            ->from('RoomTimetable')
            ->where('roomName', $roomName)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->resource;
    }
}