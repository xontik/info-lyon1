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
            <a href="#"
               class="card-title teal-text">
                <div class="card-content">
                    <i class="material-icons small">add</i>(wip)
                </div>
            </a>
        </div>
    </div>
</main>
