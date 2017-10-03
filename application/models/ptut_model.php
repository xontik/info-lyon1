<?php
/**
 * Created by PhpStorm.
 * User: Fistouil
 * Date: 01/10/2017
 * Time: 09:35
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Ptut_model extends CI_Model
{

    public function getPtutOfProf($professorId)
    {

        $sql = "SELECT nomGroupe, idGroupe, prenom FROM groupesPtut JOIN MembrePTUT USING (idGroupe) JOIN Etudiants USING (numEtudiant) WHERE idProfesseur = ?";
        return $this->db->query($sql, array($professorId))->result();
    }

    public function getPtutMembers($GroupeId){
        $sql = "SELECT nom, prenom FROM MembrePTUT m
                                   JOIN Etudiants USING (numEtudiant) 
                                   WHERE m.idGroupe = ?";
        return $this->db->query($sql, array($GroupeId))->result();
    }


}

