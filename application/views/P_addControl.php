<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 24/04/2017
 * Time: 00:33
 */
?>
<main>
    <form method="post" action="<?php echo base_url("process_professeur/addcontrol/".($data['promo']?'promo':''))?>" >
        <?php if($data['promo'] == false){ ?>
        <label for="enseignement">Selectioner un couple groupe/Matières : </label>
        <select name="enseignement" id="enseignement">
            <?php
            foreach ($data["select"] as $d) {
                echo "<option value='" . $d->idEnseignement . "'>" . $d->nomGroupe . " en " . $d->nom . "</option>";
            }
        }else{ ?>
            <label for="matiere">Selectioner une matières : </label>
            <select name="matiere" id="matiere">
                <?php
                foreach ($data["select"] as $d) {
                    echo "<option value='" . $d->codeMatière . "'>" . $d->codeMatière . " - " . $d->nom . "</option>";
                }

         } ?>
        </select><br >

        <label for="nom">Libellé : </label>
        <input type="text" id="nom"     name="nom"     /><br >
        <label for="coeff">Coefficient : </label>
        <input type="number" id="coeff"   name="coeff"   /><br >
        <label for="diviseur" >Diviseur</label>
        <input type="number" id="diviseur"name="diviseur"/><br >
        <label for="type">Type : </label>
        <input type="text" id="type"    name="type"    /><br >
        <label for="date">Date du controle : </label>
        <input type="date" id="date"    name="date"    /><br >
        <input type="submit" name="valid" value="Ajouter">
    </form>
</main>
