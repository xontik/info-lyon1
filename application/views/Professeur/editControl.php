    <?php
    /**
     * Created by PhpStorm.
     * User: xontik
     * Date: 24/04/2017
     * Time: 00:33
     */

    ?>
    <main>
        <form method="post" action="<?php echo base_url("process_professeur/editcontrole/".$data['control']->idControle)?>" >

        <label for="nom">Libellé : </label>
        <input type="text" id="nom"     name="nom"    value="<?php echo $data['control']->nomControle; ?>" /><br >
        <label for="coeff">Coefficient : </label>
        <input type="number" id="coeff"   name="coeff"  value="<?php echo $data['control']->coefficient; ?>" /><br >
        <label for="diviseur" >Diviseur</label>
        <input type="number" id="diviseur"name="diviseur" value="<?php echo $data['control']->diviseur; ?>"/><br >
        <?php
        if(is_null($data['control']->idDSPromo)){ ?>
          <label for="">Type de Controle : </label>

            <select name="typeControle" id="typeControle">

          <?php
          foreach ($data['typeControle'] as $typeControle) {
            $selected = "";
            if($data["control"] == $typeControle->idTypeControle){
              $selected = "selected";
            }
              echo '<option value="'.$typeControle->idTypeControle.'" '.$selected.'>'.$typeControle->nomTypeControle.'</option>'.PHP_EOL;
          }

          ?>
        </select>
        <br>
      <?php } ?>
        <label for="date">Date du controle : </label>
        <input type="date" id="date"    name="date"    value="<?php echo $data['control']->dateControle; ?>"/><br >
        <input type="submit" name="valid" value="Editer"> <a href="<?php echo site_url("professeur/controle")?>">Retour</a>
    </form>
</main>
