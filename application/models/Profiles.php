<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profiles extends CI_Model
{
    
    public function isPassword($userId, $password)
    {
        $res = $this->db
            ->select('password')
            ->where('idUser', $userId)
            ->get('User')
            ->row();

        if (is_null($res)) {
            return FALSE;
        }

        return password_verify($password, $res->password);
    }

    /**
     * Change the password of an user.
     *
     * @param $userId
     * @param $password
     * @return int
     */
    public function changePassword($userId, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->db
            ->set('password', $hashedPassword)
            ->where('idUser', $userId)
            ->update('User');
    }
}
