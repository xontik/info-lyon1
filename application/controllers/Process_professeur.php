<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 24/04/2017
 * Time: 01:22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Process_professeur extends CI_Controller {

    public function addcontrol($promo = ""){
        $this->load->model('control_model','ctrlMod');


        if($promo=="") {
            if (isset($_POST['enseignement']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
                && isset($_POST['date']) && isset($_POST['type'])
            ) {
                if ($_POST["enseignement"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
                    && $_POST["date"] != "" && $_POST["type"] != ""
                ) {
                    if(!$this->ctrlMod->checkEnseignementProf($_POST["enseignement"],$_SESSION['profId'])){
                        $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur cet enseignement"));
                        redirect("professeur/control");
                    }
                    if ($this->ctrlMod->addControl($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], $_POST['type'], $_POST['date'], $_POST['enseignement'])) {
                        $this->session->set_flashdata("notif", array("Controle ajoutée avec succes"));
                        redirect("professeur/control");
                    }


                }
            }
            $this->session->set_flashdata("notif", array("Erreur controle pas add"));
            redirect("professeur/control");
        }else if ($promo=="promo"){

            if (isset($_POST['matiere']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
                && isset($_POST['date'])
            ) {
                if ( $_POST["matiere"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
                    && $_POST["date"] != ""
                ) {
                    $ens = $this->ctrlMod->getEnseignementWithMatiere($_SESSION['profId'],$_POST['matiere']);
                    
                    if ($this->ctrlMod->addDsPromo($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], "DS Promo", $_POST['date'],$_POST['matiere'],$ens->idEnseignement)) {
                        $this->session->set_flashdata("notif", array("Controle promo ajoutée avec succes"));
                        redirect("professeur/control");
                    }


                }
            }
            $this->session->set_flashdata("notif", array("Erreur controle promo pas add"));
            redirect("professeur/control");
        }else{
            show_404();
        }

    }
    public function editcontrol($id = ""){
        if($id == ""){
            show_404();
        }
        $this->load->model('control_model','ctrlMod');

        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['profId'],$id)){
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/control");
        }


        if(isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
            && isset($_POST['date']) && isset($_POST['type']) ){

            if($_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
                && $_POST["date"] != "" && $_POST["type"] != "" ){

                if($this->ctrlMod->editControl($_POST['nom'],$_POST['coeff'],$_POST['diviseur'],$_POST['type'],$_POST['date'],$id)){

                    $this->session->set_flashdata("notif",array("Controle ajoutée avec succes"));
                    redirect("professeur/control");
                }




            }
        }
        $this->session->set_flashdata("notif",array("Erreur controle pas add"));
        redirect("professeur/control");

    }
    public function deletecontrol($id = "")
    {
        if($id == ""){
            show_404();
        }
        $this->load->model('control_model','ctrlMod');

        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['profId'],$id)){
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/control");
        }
        if ($this->ctrlMod->deleteControl($id)) {
            $this->session->set_flashdata("notif", array("Controle supprimé avec succes"));
            redirect("professeur/control");
        }

        $this->session->set_flashdata("notif", array("Erreur controle pas delete"));
        redirect("professeur/control");

    }



}