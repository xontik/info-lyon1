<main class="container">
    <div class="header row valign-wrapper">
        <h4 class="col">Emploi du temps de salles</h4>
        <a class="btn-flat col" href="<?= base_url('Timetable') ?>">Retour</a>
    </div>
    <div class="section row">
        <?php
        foreach ($data['rooms'] as $room) {
            ?>
            <div class="card grey lighten-3 col s12 m3 l2 offset-s1 offset-m1 offset-l1 center-align">
                <a href="<?= base_url('Timetable/room/' . $room->roomName) ?>"
                   class="card-title teal-text">
                    <div class="card-content"><?= $room->roomName ?></div>
                </a>
            </div>
            <?php
        } ?>
        <div class="card grey lighten-3 col s12 m3 l2 offset-s1 offset-m1 offset-l1 center-align">
            <a href="#add-room"
               class="card-title teal-text modal-trigger">
                <div class="card-content">
                    <i class="material-icons small">add</i>
                </div>
            </a>
        </div>
    </div>
    <div id="add-room" class="modal">
        <form action="<?= base_url('Process_Timetable/create') ?>" method="post">
            <div class="modal-content">
                <h4>Ajouter une salle</h4>
                <div class="input-field">
                    <input type="text" id="roomName" name="roomName" required>
                    <label for="roomName">Nom de la salle</label>
                </div>
                <div class="input-field">
                    <input type="text" id="url" name="url" required>
                    <label for="url">URL ou numéro de ressource</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-flat waves-effect waves-green">Créer</button>
                <a class="btn-flat modal-close waves-effect waves-red">Annuler</a>
            </div>
        </form>
    </div>
</main>
