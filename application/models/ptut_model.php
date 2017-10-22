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

        $sql = "SELECT nomGroupe, idGroupe, prenom, count(idProposition) as nbProp FROM groupesPtut JOIN MembrePTUT USING (idGroupe) 
                JOIN Etudiants USING (numEtudiant) 
                JOIN RDVPTUT USING (idGroupe) 
                JOIN PropositionsDate USING (idRDV) 
                WHERE idProfesseur = ? 
                group by prenom
                order by idGroupe";
        return $this->db->query($sql, array($professorId))->result();
    }

    public function getNbreRDV($professorId)
    {
        $sqls = "SELECT idGroupe, count(idProposition) as nbProp FROM GroupesPTUT JOIN RDVPTUT USING (idGroupe) JOIN PropositionsDate USING (idRDV) WHERE idProfesseur = ? group by idGroupe";
        return $this->db->query($sqls, array($professorId))->result();
    }

}

