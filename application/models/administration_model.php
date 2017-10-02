<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration_model extends CI_Model {

    public function getAllAdministration(){
        $sql =
        'SELECT * from Parcours
        JOIN UEdePArcours using(idParcours)
        join UE using(idue)
        join modulesdeue using(idue)
        join modules using(idmodule)
        join matieresdemodules using(idmodule)
        join matieres using(idmatiere)
        ORDER BY idParcours DESC,idUe,idModule,idmatiere';

        return $this->db->query($sql)->result();
    }

    public function getDeadlineEditable($parcours){
      $annee = $parcours['anneeCreation'];
      $cond = array(
        'S1' => $annee.'-08-31',
        'S2' => $annee.'-12-31',
        'S3' => $annee.'-08-31',
        'S4' =>  $annee.'-12-31'
      );
      return $cond[$parcours['type']];
    }
}
