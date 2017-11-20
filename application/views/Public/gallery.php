<main>
    <div>
        <a href="<?= base_url('/') ?>"
           class="btn waves-effect waves-light">Acceuil</a>
    </div>
    <div class="carousel carousel-slider">
        <div class="carousel-fixed-item">
            <div class="center hide-on-med-and-down">
                <i id="prev-slide" class="material-icons medium white-text">chevron_left</i>
                <i id="next-slide" class="material-icons medium white-text">chevron_right</i>
            </div>
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
