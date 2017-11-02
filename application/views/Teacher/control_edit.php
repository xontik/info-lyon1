<main>
    <form method="post"
          action="<?= base_url('Process_Control/update/' . $data['control']->idControle) ?>">

        <label for="nom">Libellé : </label>
        <input type="text" id="nom" name="nom" value="<?= $data['control']->nomControle ?>"/><br>
        <label for="coeff">Coefficient : </label>
        <input type="number" id="coeff" name="coeff" value="<?= $data['control']->coefficient ?>"/><br>
        <label for="diviseur">Diviseur : </label>
        <input type="number" id="diviseur" name="diviseur" value="<?= $data['control']->diviseur ?>"/><br>
        <?php
        if (is_null($data['control']->idDSPromo)) { ?>
            <select name="typeControle" id="typeControle">

                <?php
                foreach ($data['typeControle'] as $typeControle) {
                    $selected = "";
                    if ($data["control"] == $typeControle->idTypeControle) {
                        $selected = "selected";
                    }
                    echo '<option value="' . $typeControle->idTypeControle . '" ' . $selected . '>' . $typeControle->nomTypeControle . '</option>' . PHP_EOL;
                }

                ?>
            </select>
            <label for="typeControle">Type de Controle : </label>
            <br>
        <?php } ?>
        <label for="date">Date du controle : </label>
        <input type="date" id="date" name="date" value="<?php echo $data['control']->dateControle; ?>"/><br>
        <input type="submit" name="valid" value="Editer">
        <div id="return"><a href="<?= base_url('Control') ?>">Retour</a></div>
    </form>
</main>
