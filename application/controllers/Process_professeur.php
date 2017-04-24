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


        //TODO check enseignement
        if($promo=="") {
            if (isset($_POST['enseignement']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
                && isset($_POST['date']) && isset($_POST['type'])
            ) {
                if ($_POST["enseignement"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
                    && $_POST["date"] != "" && $_POST["type"] != ""
                ) {

                    if ($this->ctrlMod->addControl($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], $_POST['type'], $_POST['date'], $_POST['enseignement'])) {
                        $this->session->set_flashdata("notif", array("Controle ajoutée avec succes"));
                        redirect("professeur/control");
                    }


                }
            }
            $this->session->set_flashdata("notif", array("Erreur controle pas add"));
            redirect("professeur/control");
        }else {
            if (isset($_POST['matiere']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
                && isset($_POST['date']) && isset($_POST['type'])
            ) {
                if ( $_POST["matiere"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
                    && $_POST["date"] != "" && $_POST["type"] != ""
                ) {

                    if ($this->ctrlMod->addDsPromo($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], $_POST['type'], $_POST['date'],$_POST['matiere'])) {
                        $this->session->set_flashdata("notif", array("Controle promo ajoutée avec succes"));
                        redirect("professeur/control");
                    }


                }
            }
            $this->session->set_flashdata("notif", array("Erreur controle promo pas add"));
            redirect("professeur/control");
        }

    }
    public function editcontrol($id){
        $this->load->model('control_model','ctrlMod');



        //TODO check control id
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

    public function deletecontrol($id)
    {
        //TODO check droit pour delete
        $this->load->model('control_model', 'ctrlMod');
        if ($this->ctrlMod->deleteControl($id)) {
            $this->session->set_flashdata("notif", array("Controle supprimé avec succes"));
            redirect("professeur/control");
        }

        $this->session->set_flashdata("notif", array("Erreur controle pas delete"));
        redirect("professeur/control");

    }
}