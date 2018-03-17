<main class="container">
    <h4 class="header">Editer un contrôle</h4>

    <div class="card grey lighten-5">
        <form method="post"
              action="<?= base_url('Process_Control/update/' . $data['control']->idControl) ?>">
            <div class="card-content">
                <div class="row">
                    <div class="input-field col s12 m10 l9">
                        <input type="text" id="name" name="name" value="<?= $data['control']->controlName ?>"/>
                        <label for="name">Libellé</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6 m2">
                        <input type="number" id="coefficient" name="coefficient"
                               step="0.5" min="0" value="<?= $data['control']->coefficient ?>"/>
                        <label for="coefficient">Coefficient</label>
                    </div>
                    <div class="input-field col s6 m2">
                        <input type="number" id="divisor" name="divisor"
                               min="1" value="<?= $data['control']->divisor ?>"
                               step="1"
                                <?= $data['hasMark'] ? 'disabled' : '' ?>/>
                        <label for="divisor">Diviseur</label>
                    </div>
                    <div class="input-field col s12 m6 l5">
                        <input type="text" id="date" name="date" class="datepicker"
                               value="<?= (new DateTime($data['control']->controlDate))->format('d/m/Y') ?>"
                                <?= $data['isPast'] ? 'disabled' : '' ?>/>
                        <label for="date">Date du controle</label>
                    </div>
                </div>
            </div>
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect waves-green">Editer</button>
                <a class="btn-flat waves-effect waves-red"
                   href="<?= base_url('Process_Control/delete/' . $data['control']->idControl) ?>"
                   data-confirm="Êtes-vous certain de vouloir supprimer le contrôle <?= $data['control']->controlName ?> ?">
                    Supprimer
                </a>
                <a class="btn-flat waves-effect" href="<?= site_url('Control') ?>">Annuler</a>
            </div>
        </form>

    </div>
</main>
