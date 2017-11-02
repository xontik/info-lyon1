<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration_model extends CI_Model
{

    public function getAllAdministration()
    {
        $sql =
            'SELECT *
            FROM Parcours
            JOIN UEdePArcours USING (idParcours)
            JOIN UE USING (idue)
            JOIN modulesdeue USING (idue)
            JOIN modules USING (idmodule)
            JOIN matieresdemodules USING (idmodule)
            JOIN matieres USING (idmatiere)
            ORDER BY idParcours DESC, idUe, idModule, idmatiere';

        return $this->db->query($sql)->result();
    }


    public function getUENotInParcours($idParcours)
    {
        $sql =
            'SELECT idUE,codeUE,nomUE,anneeCreation FROM UE
            WHERE idUe NOT IN (
                SELECT idUE
                FROM UEdePArcours
                WHERE idParcours = ?
            )
            GROUP BY idUE
            ORDER BY anneeCreation DESC';

        return $this->db->query($sql, array($idParcours))->result();
    }

    public function getUEInParcours($idParcours)
    {
        $sql =
            'SELECT *
            FROM UE
            JOIN UEdePArcours USING (idUE)
            WHERE idParcours = ?
            ORDER BY idUE';

        return $this->db->query($sql, array($idParcours))->result();
    }

    //TODO LES DATEs A METTRE EN CONFIG
    public function getAllParcoursEditable()
    {
        $sql =
            'SELECT idParcours, type, anneeCreation
            FROM Parcours
            LEFT JOIN Semestres USING (idParcours)
            WHERE DATE(CONCAT(anneeCreation, \'-08-31\')) > CURDATE()
            GROUP BY idParcours';

        return $this->db->query($sql)->result();
    }

    public function isThisParcoursExist($id)
    {
        return $this->db->query('SELECT * FROM Parcours WHERE idParcours = ?', array($id))
                ->num_rows() > 0;
    }

    public function getParcoursType($id)
    {
        return $this->db->query('SELECT type FROM Parcours WHERE idParcours = ?', array($id))
            ->row()
            ->type;
    }

    public function getAllLastParcours()
    {
        $sql = 'SELECT * FROM Parcours ORDER BY type';
        return $this->db->query($sql)->result();
    }

    public function getAllUEParcours()
    {
        $sql =
            'SELECT DISTINCT idUE, nomUE, codeUE, Parcours.anneeCreation, idParcours
            FROM Parcours
            JOIN UEdePArcours USING (idParcours)
            JOIN UE USING (idue)
            ORDER BY idParcours DESC';

        return $this->db->query($sql)->result();
    }

    public function isThisParcoursEditable($id)
    {
        return $this->db
            ->where('DATE(CONCAT(anneeCreation,\'-08-31\')) > CURDATE()')
            ->where('idParcours', $id)
            ->get('Parcours')
            ->num_rows() > 0;
    }

    public function addUEtoParcours($idParcours, $idUE)
    {
        $data = array(
            'idParcours' => $idParcours,
            'idUE' => $idUE
        );
        return $this->db->insert('UEDeParcours', $data);
    }

    public function removeUEtoParcours($idParcours, $idUE)
    {
        return $this->db->where('idUE', $idUE)
            ->where('idParcours', $idParcours)
            ->delete('UEDeParcours');
    }

    public function addParcours($date, $type)
    {
        $data = array(
            'anneeCreation' => $date,
            'type' => $type
        );
        return $this->db->insert('Parcours', $data);
    }

    public function deleteCascadeParcours($id)
    {
        return $this->db->delete('Parcours', array('idParcours' => $id));
    }

    public function addGroupe($idSem, $nomGroupe)
    {
        if ($this->isGroupeAlreadyExist($idSem, $nomGroupe)) {
            return false;
        } else {
            $data = array(
                'idSemestre' => $idSem,
                'nomGroupe' => $nomGroupe
            );
            return $this->db->insert('Groupes', $data);
        }
    }

    public function isGroupeAlreadyExist($idSem, $nomGroupe)
    {
        $sql = 'SELECT * FROM groupes WHERE idSemestre = ? AND nomGroupe = ?';
        return $this->db->query($sql, array($idSem, $nomGroupe))
                ->num_rows() > 0;
    }

    public function isGroupeEditable($idGroupe)
    {
        $CI =& get_instance();
        $CI->load->model('semester_model', 'semMod');

        $sql = 'SELECT * FROM Groupes WHERE idGroupe = ?';
        $group = $this->db->query($sql, array($idGroupe))
            ->row();

        if (!is_null($group)) {
            return $CI->semMod->isSemesterEditable($group->idSemestre);
        }
        return false;

    }

    public function deleteGroupe($idGroupe)
    {
        return $this->db->delete('Groupes', array('idGroupe' => $idGroupe));
    }

    public function getGroupDetails($idGroup)
    {
        $sql =
            'SELECT *
            FROM Groupes
            JOIN Semestres USING (idSemestre)
            JOIN Parcours USING (idParcours)
            WHERE idGroupe = ?';

        return $this->db->query($sql, array($idGroup))->row();
    }

}
