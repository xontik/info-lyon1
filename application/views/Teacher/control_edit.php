<main class="container">
    <h4 class="header">Editer un contrôle</h4>
    <form method="post"
          action="<?= base_url('Process_Control/update/' . $data['control']->idControl) ?>">
        <div class="section">
            <div class="row">
                <div class="input-field col s12 l8">
                    <input type="text" id="name" name="name" value="<?= $data['control']->controlName ?>"/>
                    <label for="name">Libellé</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6 m4 l1">
                    <input type="number" id="coefficient" name="coefficient" step="0.5" min="0" value="<?= $data['control']->coefficient ?>"/>
                    <label for="coefficient">Coefficient</label>
                </div>
                <div class="input-field col s6 m4 l1">
                    <input type="number" id="divisor" name="divisor" min="1" value="<?= $data['control']->divisor ?>"/>
                    <label for="divisor">Diviseur</label>
                </div>
                <div class="input-field col s12 m6 l5">
                    <input type="text" id="date" name="date" class="datepicker"
                           value="<?= (new DateTime($data['control']->controlDate))->format('d/m/Y') ?>"/>
                    <label for="date">Date du controle</label>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <div class="section btn-footer">
            <button type="submit" class="btn waves-effect">Editer</button>
            <a class="btn-flat" href="<?= site_url('Control') ?>">Annuler</a>
        </div>
    </form>
</main>
