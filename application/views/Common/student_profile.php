<main>
    <div class="container">
        <h4>Profil de : <?= $data['student']->name . ' ' . $data['student']->surname?></h4>
        <?php
        foreach ($data['semesters'] as $key => $semester) { ?>
            <div class="card grey lighten-5 expandable">
                <div class="card-content">
                    <div class="card-title"><?= $semester->groupName . $semester->courseType . ' '
                        . ($semester->delayed ? 'différé' : '') . ' de '
                        . $semester->schoolYear . '-' . ($semester->schoolYear + 1) ?>
                        <span class='right'><i class="material-icons medium">expand_more</i></span>
                    </div>
                    <div class="row">
                            <div class="col s12">
                                <div class="card grey lighten-4">
                                    <div class="card-content">
                                        <span class="card-title">Absences</span>
                                        <?php
                                        if ($data['absences'][$semester->idSemester]['justified'] === 0
                                            && $data['absences'][$semester->idSemester]['unjustified'] === 0
                                        ) { ?>
                                            <p>Pas d'absences</p>
                                            <?php
                                        } else { ?>
                                            <p><?= $data['absences'][$semester->idSemester]['unjustified'] ?> absence(s) injustifiée(s)</p>
                                            <p><?= $data['absences'][$semester->idSemester]['justified'] ?> absence(s) justifiée(s)</p>
                                            <?php
                                        }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12">
                                <div class="card grey lighten-4">
                                    <div class="card-content">
                                        <div class="card-title">
                                            Notes
                                            <span class='right'><?= is_null($data['totalAvgs'][$semester->idSemester]['student'])?'':'Moyenne du semestre : ' . number_format($data['totalAvgs'][$semester->idSemester]['student'], 2) . '/20'?></span>
                                        </div>
                                        <?php if (count($data['averageBySemester'][$semester->idSemester])){ ?>
                                        <ul class="collection with-header">
                                                <?php $lastTuId = 0;
                                                foreach ($data['averageBySemester'][$semester->idSemester] as $average):
                                                    if ($lastTuId != $average->idTeachingUnit) {
                                                        if ($lastTuId != 0) {
                                                            echo '</ul><ul class="collection with-header">';
                                                        }
                                                        $lastTuId = $average->idTeachingUnit;
                                                        ?>
                                                        <li class="collection-header">
                                                            <div class="row no-margin">
                                                                <div class="col s9">
                                                                    <h5><?= $average->teachingUnitCode . ' ' . $average->teachingUnitName?></h5>
                                                                </div>

                                                                <div class="col s3 right-align">
                                                                    <span>
                                                                        Eleve : <?=$data['averageTUBySemester'][$semester->idSemester][$average->idTeachingUnit]->average?>/20<br>
                                                                        Groupe : <?= $data['averageTUBySemester'][$semester->idSemester][$average->idTeachingUnit]->groupAverage ?>/20
                                                                    </span>
                                                                </div>
                                                            </div>
                                                    <?php } ?>
                                                    <li class="collection-item"><?= $average->subjectCode . ' ' . ($average->moduleName?$average->moduleName.' : ':'') . $average->subjectName?>
                                                        <span class='right'>Eleve : <?=$average->average?>/20 | Groupe : <?= $average->groupAverage ?>/20</span></li>
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
            <?php
        } ?>
    </div>
</main>
