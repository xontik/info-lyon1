<main>
    <div class="carousel carousel-slider">
        <div class="carousel-fixed-item center hide-on-med-and-down">
            <i id="prev-slide" class="material-icons medium white-text">chevron_left</i>
            <i id="next-slide" class="material-icons medium white-text">chevron_right</i>
        </div>
        <?php
        foreach ($data['projects'] as $project) { ?>
            <div class="carousel-item center-align"><img src="/assets/images/projects/<?= $project->projectPicture ?>"></div>
            <?php
        } ?>
    </div>
</main>
