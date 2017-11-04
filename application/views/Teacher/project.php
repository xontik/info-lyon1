<main>
    <h4>Projets Tuteurés</h4>
    <?php
    foreach ($data['projects'] as $project)
    { ?>
        <p><a href="<?= base_url('/Project/detail/' . $project->idProject) ?>"><?= $project->projectName ?></a></p>
        <?php
    }
    ?>
</main>
