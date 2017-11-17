<main class="container">
    <h4>Projets Tuteurés</h4>
    <div class="row grid">
        <?php
        if (count($data['projects'])) {
            foreach ($data['projects'] as $project)
            { ?>
                <div class="col s12 m6 l4">
                    <div class="card grey lighten-5">
                        <div class="card-content">
                            <span class="card-title"><?= $project->projectName?$project->projectName:'Nouveau Projet'?></span>
                            <?php if (!empty($data['members'][$project->idProject])): ?>
                                <ul class="collection">
                                    <?php foreach ($data['members'][$project->idProject] as $member): ?>
                                        <li class="collection-item"><?= $member->name ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="card-action">
                            <a href="<?= base_url('/Project/appointment/' . $project->idProject) ?>">Suivi</a>
                            <a href="<?= base_url('/Project/manage/' . $project->idProject) ?>">Gérer</a>
                        </div>
                    </div>
                </div>

                <?php

            }
        } else { ?>
                    <p>Aucun projet</p>
        <?php } ?>
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large" href="<?= base_url('/Process_Project/create') ?>">
                <i class="large material-icons">add</i>
            </a>
        </div>


    </div>
</main>
