<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Control_model extends CI_Model
{

    public function getControl($controlId)
    {
        return $this->db->where('idControle', $controlId)
            ->get('Controles')
            ->row();
    }

    public function getControls($professorId)
    {
        $sql = 'SELECT foo.codeMatiere,foo.idMatiere,foo.nomMatiere,foo.idControle,foo.nomControle,
        foo.coefficient,foo.diviseur,foo.nomTypeControle,foo.idTypeControle,foo.median,foo.average,
        foo.dateControle,foo.coefficientMatiere,foo.nomGroupe,foo.idGroupe
        FROM (
          SELECT codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
          coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
          dateControle,coefficientMatiere,nomGroupe,idGroupe FROM Controles
            JOIN TypeControle USING (idTypeControle)
            JOIN Enseignements USING (idEnseignement)
            JOIN Matieres USING (idMatiere)
            JOIN Groupes USING (idGroupe)
            JOIN Semestres USING (idSemestre)
            WHERE idProfesseur = ? AND actif = 1
          UNION
          SELECT DISTINCT codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
          coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
          dateControle,coefficientMatiere,NULL AS nomGroupe,NULL AS idGroupe FROM Controles
            JOIN TypeControle USING (idTypeControle)
            JOIN DsPromo USING (idDSPromo)
            JOIN Matieres USING (idMatiere)
            JOIN Enseignements USING (idMatiere)
            JOIN Semestres USING (idSemestre)
            WHERE idProfesseur = ? AND actif = 1
          UNION
          SELECT codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
          coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
          dateControle,coefficientMatiere,nomGroupe,idGroupe FROM Controles
            JOIN TypeControle USING (idTypeControle)
            JOIN Enseignements USING (idEnseignement)
            JOIN Matieres USING (idMatiere)
            JOIN Groupes USING (idGroupe)
            JOIN MatieresDeModules USING (idMatiere)
            JOIN Referents USING (idModule,idSemestre)
            JOIN Semestres USING (idSemestre)
            WHERE Referents.idProfesseur = ? AND  actif = 1
          UNION
          SELECT codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
          controles.coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
          dateControle,coefficientMatiere,NULL AS nomGroupe,NULL AS idGroupe FROM Controles
            JOIN TypeControle USING (idTypeControle)
            JOIN DsPromo USING (idDSPromo)
            JOIN Matieres USING (idMatiere)
            JOIN MatieresDeModules USING (idMatiere)
            JOIN Modules USING (idModule)
            JOIN Referents USING (idModule,idSemestre)
            JOIN Semestres USING (idSemestre)
    
            WHERE idProfesseur = ? AND actif = 1
        ) AS foo ';

        //TODO continuer de verifier les different cas pour les ds surtout via Referents
        return $this->db->query($sql, array($professorId, $professorId, $professorId, $professorId))
            ->result();
    }


    public function isReferent($profId, $controlId)
    {
        $sql = 'SELECT count(*) AS nb
            FROM Referents
            JOIN MatieresDeModules USING (idModule)
            LEFT JOIN DsPromo USING (idMatiere)
            JOIN Controles USING (idDSPromo)
            WHERE idControle = ?
            AND idProfesseur = ?';

        return $this->db->query($sql, array($controlId, $profId))
                ->row()
                ->nb > 0;
    }

    public function checkProfessorRightOnControl($profId, $controlId)
    {
        $ids = array_merge($this->getControlIdsFromTeacherStatus($profId),
            $this->getControlIdsFromReferentStatus($profId));

        foreach ($ids as $id) {
            if ($id->idControle == $controlId) {
                return true;
            }
        }
        return false;
    }

    public function getControlIdsFromTeacherStatus($profId)
    {
        $sql = 'SELECT idControle
            FROM Controles
            JOIN Enseignements USING (idEnseignement)
            JOIN groupes USING (idGroupe)
            JOIN Semestres USING (idSemestre)
            WHERE idProfesseur = ? AND actif = 1
            UNION
            SELECT idControle
            FROM Controles
            JOIN DsPromo USING (idDSPromo)
            JOIN Semestres USING (idSemestre)
            JOIN Enseignements USING (idMatiere)
            WHERE idProfesseur = ? AND actif = 1';

        return $this->db->query($sql, array($profId, $profId))
            ->result();
    }

    public function getControlIdsFromReferentStatus($profId)
    {
        $sql = 'SELECT idControle
            FROM Referents
            JOIN MatieresDeModules USING (idModule)
            JOIN Enseignements USING (idMatiere)
            JOIN Semestres USING (idSemestre)
            JOIN Controles USING (idEnseignement)
            WHERE Referents.idProfesseur = ? AND actif = 1
            UNION
            SELECT idControle
            FROM Referents
            JOIN MatieresDeModules USING (idModule)
            JOIN DsPromo USING (idMatiere,idSemestre)
            JOIN Semestres USING (idSemestre)
            JOIN Controles USING (idDSPromo)
            WHERE Referents.idProfesseur = ? AND actif = 1';

        return $this->db->query($sql, array($profId, $profId))->result();

    }

    public function getCurrentSemestreFromMatiere($idMat)
    {
        $sql = 'SELECT idSemestre
            FROM Matieres
            JOIN MatieresDeModules USING (idMatiere)
            JOIN ModulesDeUE USING (idModule)
            JOIN UEdeParcours USING (idUE)
            JOIN Parcours USING (idParcours)
            JOIN Semestres USING (idParcours)
            WHERE idMatiere = ? AND actif = 1';

        return $this->db->query($sql, array($idMat))
            ->row()
            ->idSemestre;
    }

    public function addDsPromo($nom, $coeff, $div, $type, $date, $idMat)
    {
        $data = array(
            'idSemestre' => $this->ctrlMod->getCurrentSemestreFromMatiere($idMat),
            'idMatière' => $idMat
        );
        $this->db->insert('DSPromo', $data);

        $idpromo = $this->db->insert_id();

        $data = array(
            'nomControle' => $nom,
            'coefficient' => $coeff,
            'diviseur' => $div,
            'idTypeControle' => $type,
            'dateControle' => $date,
            'idDSPromo' => $idpromo
        );
        return $this->db->insert('Controles', $data);
    }

    public function addControl($nom, $coeff, $div, $type, $date, $idEnseignement)
    {
        $data = array(
            'nomControle' => $nom,
            'coefficient' => $coeff,
            'diviseur' => $div,
            'idTypeControle' => $type,
            'dateControle' => $date,
            'idEnseignement' => $idEnseignement
        );
        return $this->db->insert('Controles', $data);
    }

    public function editControl($nom, $coeff, $div, $type, $date, $controlId)
    {
        $data = array(
            'nomControle' => $nom,
            'coefficient' => $coeff,
            'diviseur' => $div,
            'idTypeControle' => $type,
            'dateControle' => $date,
        );

        return $this->db->set($data)
            ->where('idControle', $controlId)
            ->update('Controles');
    }

    public function deleteControl($controlId)
    {
        return $this->db->delete('Controles', array('idControle' => $controlId));
    }

    public function getEnseignements($profId)
    {

        $sql = 'SELECT DISTINCT * FROM (
            (
                SELECT nomGroupe, nomMatiere, idEnseignement
                FROM Enseignements
                JOIN Groupes USING (idGroupe)
                JOIN Matieres USING (idMatiere)
                JOIN Semestres USING (idSemestre)
                WHERE idProfesseur = ? AND actif = 1
            )
            UNION
            (
                SELECT nomGroupe, nomMatiere, idEnseignement
                FROM Referents
                JOIN MatieresDeModules USING (idModule)
                JOIN Matieres USING (idMatiere)
                JOIN Enseignements USING (idMatiere)
                JOIN Semestres USING (idSemestre)
                JOIN Groupes USING (idGroupe,idSemestre)
                WHERE Referents.idProfesseur = ? AND actif = 1
            )
        ) AS foo ';

        return $this->db->query($sql, array($profId, $profId))->result();
    }

    public function getGroupes($profId)
    {
        $sql = 'SELECT DISTINCT * FROM (
            (
                SELECT nomGroupe, type, idGroupe
                FROM Enseignements
                JOIN Groupes USING (idGroupe)
                JOIN Semestres USING (idSemestre)
                JOIN Parcours USING (idParcours)
                WHERE idProfesseur = ? AND actif = 1
            )
            UNION
            (
                SELECT nomGroupe, type, idGroupe
                FROM Referents
                JOIN MatieresDeModules USING (idModule)
                JOIN Enseignements USING (idMatiere)
                JOIN Groupes USING (idGroupe,idSemestre)
                JOIN Semestres USING (idSemestre)
                JOIN Parcours USING (idParcours)
                WHERE Referents.idProfesseur = ? AND actif = 1
            )
        ) AS foo
        ORDER BY type';

        return $this->db->query($sql, array($profId, $profId))->result();
    }

    public function checkEnseignementProf($ens, $prof)
    {
        $sql = 'SELECT idEnseignement FROM Enseignements
                JOIN groupes USING (idGroupe)
                JOIN Semestres USING (idSemestre)
                WHERE  idEnseignement = ? AND actif = 1 AND idProfesseur = ?
            UNION
                SELECT idEnseignement FROM Referents
                JOIN MatieresDeModules USING (idModule)
                JOIN Enseignements USING (idMatiere)
                JOIN Semestres USING (idSemestre)
                WHERE  idEnseignement = ? AND actif = 1
                AND Referents.idProfesseur = ?';

        return count($this->db->query($sql, array($ens, $prof, $ens, $prof))->row()) > 0;
    }

    public function getMatieres($profId)
    {
        $sql = 'SELECT DISTINCT idMatiere, codeMatiere, nomMatiere
                FROM Enseignements
                JOIN Groupes USING (idGroupe)
                JOIN Matieres USING (idMatiere)
                JOIN Semestres USING (idSemestre)
                WHERE idProfesseur = ? AND actif = 1
            UNION
                SELECT DISTINCT idMatiere,codeMatiere,nomMatiere FROM Referents
                JOIN MatieresDeModules USING ( idModule)
                JOIN Matieres USING (idMatiere)
                JOIN Semestres USING (idSemestre)
                WHERE idProfesseur = ? AND actif = 1';

        return $this->db->query($sql, array($profId, $profId))
            ->result();
    }

    public function getMatiere($idControle)
    {
        $sql = 'SELECT DISTINCT * FROM (
                    SELECT codeMatiere, nomMatiere
                    FROM Controles
                    JOIN DsPromo USING (idDSPromo)
                    JOIN Matieres USING (idMatiere)
                    WHERE idControle = ?
                UNION
                    SELECT codeMatiere, nomMatiere
                    FROM Controles
                    JOIN Enseignements USING (idEnseignement)
                    JOIN Matieres USING (idMatiere)
                    WHERE idControle = ?
            ) AS foo';

        return $this->db->query($sql, array($idControle, $idControle))
            ->row();
    }

    public function getTypeControle()
    {
        return $this->db->get('TypeControle')
            ->result();
    }
}
