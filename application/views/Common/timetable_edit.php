<main class="container">
    <div class="row valign-wrapper">
        <h4 class="header col">Modifier votre emploi du temps</h4>
        <a class="btn-flat waves-effect col" href="<?= base_url('Timetable') ?>">Retour</a>
    </div>

    <div class="row">
        <div class="col s12 m10 l8 card grey lighten-5">
            <div class="card-content">
                <span class="card-title">Trouvez votre numéro de ressource</span>
                <ol>
                    <li>Accédez à l'emploi du de votre groupe sur l'application <a href="http://adelb.univ-lyon1.fr">ADE WEB</a></li>
                    <li>Cliquez sur l'icone <img src="<?= img_url('agenda_export.png') ?>" alt="Agenda export" width="32"  height="24"> puis <b>Générer URL</b></li>
                    <li>Copier l'url générée et la coller <b>ci-dessous</b></li>
                    <li><i class="material-icons">warning</i> Cette opération est a effectuer à chaque changement d'emploi du temps</li>
                </ol>
                <form action="<?= base_url('Process_Timetable/edit'
                        . (isset($data['type']) && isset($data['who']) ? '/' . $data['type'] . '/' . $data['who'] : '')) ?>"
                      method="post">
                    <div class="input-field">
                        <input type="text" name="url" id="url">
                        <label for="url">Collez ici l'url ou le numéro de ressource</label>
                    </div>
                    <button type="submit" class="btn-flat waves-effects">Envoyer l'url</button>
                </form>
            </div>
        </div>
    </div>
</main>
