<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 24/04/2017
 * Time: 00:33
 */

?>
<main>
    <form method="post" action="<?php echo base_url("process_professeur/editcontrol/".$data['control']->idControle)?>" >

        <label for="nom">Libellé : </label>
        <input type="text" id="nom"     name="nom"    value="<?php echo $data['control']->nomControle; ?>" /><br >
        <label for="coeff">Coefficient : </label>
        <input type="number" id="coeff"   name="coeff"  value="<?php echo $data['control']->coefficient; ?>" /><br >
        <label for="diviseur" >Diviseur</label>
        <input type="number" id="diviseur"name="diviseur" value="<?php echo $data['control']->diviseur; ?>"/><br >
        <label for="type">Type : </label>
        <input type="text" id="type"    name="type"    value="<?php echo $data['control']->typeControle; ?>"/><br >
        <label for="date">Date du controle : </label>
        <input type="date" id="date"    name="date"    value="<?php echo $data['control']->dateControle; ?>"/><br >
        <input type="submit" name="valid" value="Editer"> <a href="<?php echo site_url("professeur/control")?>">Retour</a>
    </form>
</main>
