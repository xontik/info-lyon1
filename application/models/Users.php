<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Model
{

    /**
     * If password matches, obtain user informations needed for session data
     *
     * @param string $login
     * @param string $password
     * @return array|bool FALSE if login or password is invalid
     */
    public function getUserInformations($login, $password)
    {
        static $student_regex = '/^p[0-9]{7}$/';
        static $teacher_secretariat_regex = '/^[a-z]*\.[a-z]*$/';

        if (preg_match($student_regex, $login))
        {
            return $this->_getStudentInformations($login, $password);
        }
        else if (preg_match($teacher_secretariat_regex, $login)) {
            return $this->_getTeacherOrSecretaryInformations($login, $password);
        }
        else {
            $_SESSION['form_errors']['id'] = 'Identifiant invalide';
            return FALSE;
        }
    }

    /**
     * @param int $studentId
     * @param string $password
     * @return array|bool
     */
    private function _getStudentInformations($studentId, $password)
    {
        $data = $this->db->select('idStudent, surname, name, email, password, idUser')
            ->from('Student')
            ->join('User', 'idUser')
            ->where('idStudent', $studentId)
            ->get()
            ->row();

        if (empty($data)) {
            // ID doesn't exists
            $_SESSION['form_errors']['id'] = 'Identifiant incorrect';
            return FALSE;
        }

        if (!password_verify($password, $data->password)) {
            // Pass doesn't match
            $_SESSION['form_errors']['password'] = 'Mot de passe incorrect';
            return FALSE;
        }

        return array(
            'userId' => $data->idUser,
            'userType' => 'student',
            'id' => $data->idStudent,
            'name' => $data->name,
            'surname' => $data->surname,
            'email' => $data->email
        );
    }

    /**
     * @param string $login
     * @param $password
     * @return array|bool
     */
    private function _getTeacherOrSecretaryInformations($login, $password)
    {
        $searchData = explode('.', $login);
        $isTeacher = true;

        $data = $this->db->select('idTeacher, surname, name, email, password, idUser')
            ->from('Teacher')
            ->join('User', 'idUser')
            ->where('LOWER(name)', $searchData[0])
            ->where('LOWER(surname)', $searchData[1])
            ->get()
            ->row();

        if (empty($data)) {
            // Not a teacher, try with secretariat
            $isTeacher = false;
            $data = $this->db->select('idSecretariat, surname, name, email, password, idUser')
                ->from('Secretariat')
                ->join('User', 'idUser')
                ->where('LOWER(name)', $searchData[0])
                ->where('LOWER(surname)', $searchData[1])
                ->get()
                ->row();

            if (empty($data)) {
                $_SESSION['form_errors']['id'] = 'Identifiant inconnu';
                return FALSE;
            }
        }

        if (!password_verify($password, $data->password)) {
            // Pass doesn't match
            $_SESSION['form_errors']['id'] = 'Mot de passe invalide';
            return FALSE;
        }

        return array(
            'userId' => $data->idUser,
            'userType' => $isTeacher ? 'teacher' : 'secretariat',
            'id' => $isTeacher ? $data->idTeacher : $data->idSecretariat,
            'name' => $data->name,
            'surname' => $data->surname,
            'email' => $data->email
        );
    }
}
