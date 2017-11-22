<main>
    <div class="carousel carousel-slider">
        <div class="carousel-fixed-item">
            <i id="prev-slide" class="material-icons medium white-text">chevron_left</i>
            <b><a href="<?= base_url('/') ?>"
               class="btn-flat white-text waves-effect waves-light">Accueil</a></b>
            <i id="next-slide" class="material-icons medium white-text">chevron_right</i>
        </div>
        <?php
        if (empty($data['projects'])) { ?>
            <div class="carousel-item center-align">
                Désolé, nous n'avons de d'illustrations à vous montrer pour l'instant !
            </div>
            <?php
        } else {
            foreach ($data['projects'] as $project) { ?>
                <div class="carousel-item center-align">
                    <img src="/assets/images/projects/<?= $project->projectPicture ?>">
                </div>
                <?php
            }
        } ?>
    </div>
</main>
