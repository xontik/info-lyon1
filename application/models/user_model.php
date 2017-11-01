<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user_model extends CI_Model
{

    /**
     * If password matches, obtain user informations needed for session data
     *
     * @param $userid
     * @param $password
     * @return array|bool The informations
     */
    public function getUserInformations($userid, $password)
    {
        static $student_regex = '/^p[0-9]{7}$/';
        static $teacher_secretariat_regex = '/^[a-z]*\.[a-z]*$/';

        if (preg_match($student_regex, $userid))
        {
            return $this->getStudentInformations($userid, $password);
        }
        else if (preg_match($teacher_secretariat_regex, $userid)) {
            return $this->getTeacherOrSecretaryInformations($userid, $password);
        }
        else {
            $_SESSION['form_errors']['id'] = 'Identifiant invalide';
            return FALSE;
        }
    }

    private function getStudentInformations($studentId, $password)
    {
        $data = $this->db->select('numEtudiant, nom, prenom, mail, password')
            ->from('Etudiants')
            ->where('numEtudiant', $studentId)
            ->get()
            ->result();

        if (empty($data)) {
            // ID doesn't exists
            $_SESSION['form_errors']['id'] = 'Impossible de retrouver le couple identifiant/mot de passe';
            return FALSE;
        }

        // Get first and only line
        $data = $data[0];

        if (!password_verify($password, $data->password)) {
            // Pass doesn't match
            $_SESSION['form_errors']['id'] = 'Impossible de retrouver le couple identifiant/mot de passe';
            return FALSE;
        }

        return array(
            'user_type' => 'student',
            'id' => $data->numEtudiant,
            'name' => $data->prenom,
            'surname' => $data->nom,
            'mail' => $data->mail
        );
    }

    private function getTeacherOrSecretaryInformations($userid, $password)
    {
        $search_data = explode('.', $userid);
        $is_teacher = true;

        $data = $this->db->select('idProfesseur, nom, prenom, mail, password')
            ->from('Professeurs')
            ->where('LOWER(prenom)', $search_data[0])
            ->where('LOWER(nom)', $search_data[1])
            ->get()
            ->row();

        if (empty($data)) {
            // Not a teacher, try with secretariat
            $is_teacher = false;
            $data = $this->db->select('idSecretaire, nom, prenom, mail, password')
                ->from('Secretariat')
                ->where('LOWER(prenom)', $search_data[0])
                ->where('LOWER(nom)', $search_data[1])
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
            'user_type' => $is_teacher ? 'teacher' : 'secretariat',
            'id' => $is_teacher ? $data->idProfesseur : $data->idSecretaire,
            'name' => $data->prenom,
            'surname' => $data->nom,
            'mail' => $data->mail
        );
    }
}
