<main class="container">
    <h4 class="header">Modifier votre emploi du temps</h4>
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
                <form action="<?= base_url('Process_Timetable/edit') ?>" method="post">
                    <div class="input-field">
                        <input type="text" name="url" id="url">
                        <label for="url">Collez ici l'url ou le numéro de ressource</label>
                    </div>
                    <div class="btn-footer">
                        <button type="submit" class="btn waves-effects">Envoyer l'url</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
