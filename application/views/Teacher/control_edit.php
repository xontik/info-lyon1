<main>
    <form method="post"
          action="<?= base_url('Process_Control/update/' . $data['control']->idControl) ?>">

        <label for="nom">Libellé</label>
        <input type="text" id="nom" name="nom" value="<?= $data['control']->controlName ?>"/><br>
        <label for="coeff">Coefficient</label>
        <input type="number" id="coeff" name="coeff" value="<?= $data['control']->coefficient ?>"/><br>
        <label for="divisor">Diviseur</label>
        <input type="number" id="divisor" name="divisor" value="<?= $data['control']->divisor ?>"/><br>
        <?php
        if (is_null($data['control']->idPromo)) { ?>
            <select name="typeId" id="typeId">
                <?php
                foreach ($data['controlTypes'] as $controlType) {
                    $selected = '';
                    if ($data['control'] == $controlType->idControlType) {
                        $selected = 'selected';
                    }
                    ?>
                    <option value="<?= $controlType->idControlType ?>" <?= $selected ?>
                        ><?= $controlType->controlTypeName ?>
                    </option>
                    <?php
                } ?>
            </select>
            <label for="typeId">Type de Controle</label>
            <br>
        <?php } ?>
        <label for="date">Date du controle : </label>
        <input type="date" id="date" name="date" value="<?= $data['control']->controlDate ?>">
        <button type="submit">Editer</button>
        <a href="<?= base_url('Control') ?>">Retour</a>
    </form>
</main>
