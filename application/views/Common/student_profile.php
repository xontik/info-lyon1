<main>
    <div class="container">
        <h4>Profil de : <?= $data['student']->name . ' ' . $data['student']->surname?></h4>
        <?php foreach ($data['semesters'] as $key => $semester): ?>
            <div class="card grey lighten-5 expandable">
                <div class="card-content">
                    <div class="card-title"><?= $semester->groupName . $semester->courseType . ' '
                        . ($semester->delayed?'différé':'') . ' de '
                        . $semester->schoolYear . '-' . ($semester->schoolYear + 1) ?>
                        <span class='right'><i class="material-icons medium">expand_more</i></span>

                    </div>
                    <div class="row">
                            <div class="col s12">
                                <ul class="card grey lighten-3">
                                    <div class="card-content">
                                        <span class="card-title">Absences</span>
                                        <?php if (count($data['absences'][$semester->idSemester])) {?>
                                            <p>Des absences mais pas encore affichées</p>
                                        <!-- TODO affichage-->
                                        <?php } else { ?>
                                            <p>Aucune absence pour la periode</p>
                                        <?php }?>
                                    </div>
                                </ul>
                            </div>
                            <div class="col s12">
                                <div class="card grey lighten-4">
                                    <div class="card-content">
                                        <span class="card-title">Notes</span>
                                        <?php if (count($data['averageBySemester'][$semester->idSemester])){ ?>
                                        <ul class="collection with-header">
                                                <?php $lastTuId = 0;
                                                foreach ($data['averageBySemester'][$semester->idSemester] as $average):
                                                    if ($lastTuId != $average->idTeachingUnit) {
                                                        $lastTuId = $average->idTeachingUnit; ?>

                                                        <li class="collection-header">
                                                            <div class="row">
                                                                <div class="col s9">
                                                                    <h5><?= $average->teachingUnitCode . ' ' . $average->teachingUnitName?></h5>
                                                                </div>

                                                                <div class="col s3 right-align">
                                                                    <span>Eleve : <?=$data['averageTUBySemester'][$semester->idSemester][$average->idTeachingUnit]->average?> <br> Groupe : <?= $data['averageTUBySemester'][$semester->idSemester][$average->idTeachingUnit]->groupAverage ?></span>
                                                                </div>
                                                            </div>
                                                    <?php } ?>
                                                    <li class="collection-item"><?= $average->subjectCode . ' ' . ($average->moduleName?$average->moduleName.' : ':'') . $average->subjectName?>
                                                        <span class='right'>Eleve : <?=$average->average?> | Groupe : <?= $average->groupAverage ?></span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php } else { ?>
                                            <p>Aucune note pour la periode</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
            <?php if ($key < count($data['semesters'])-1) { ?>
                <div class="center">
                    <i class="material-icons medium">expand_less</i>
                </div>
            <?php } ?>
        <?php endforeach; ?>

    </div>
</main>
