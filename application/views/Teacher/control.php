    <main class="container">
        <h4>Contrôles</h4>
        <div class="card grey lighten-5">
            <div class="card-content">
                <div class="section col s12 m12 l10 offset-l1 no-pad-bot">
                    <form method="post" action="<?= base_url('Control')?>">
                        <div class="row valign-wrapper">
                            <div class="input-field col s12 m6 l4">
                                <select name="groupId" id="groupId">
                                    <option value="">Tous</option>
                                    <?php
                                    if (isset($data['groups']) && count($data['groups'])) {
                                        foreach ($data['groups'] as $group) {
                                            $selected = isset($data['restrict']['group'])
                                                && $data['restrict']['group'] == $group->idGroup
                                                ? 'selected' : '';
                                            ?>
                                            <option value="<?= $group->idGroup ?>" <?= $selected ?>
                                                ><?= $group->groupName . $group->courseType ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="groupId">Groupe</label>
                            </div>
                            <div class="input-field col s12 m6 l4">
                                <select name="subjectId" id="subjectId">
                                    <option value="">Tous</option>
                                    <?php
                                    if (isset($data['subjects'])) {
                                        foreach ($data['subjects'] as $subject) {
                                            $selected = isset($data['restrict']['subject'])
                                            && $data['restrict']['subject'] == $subject->idSubject
                                                ? 'selected' : '';
                                            ?>
                                            <option value="<?= $subject->idSubject ?>" <?= $selected ?>
                                                ><?= $subject->subjectCode . ' - ' . $subject->subjectName ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="subjectId">Matières</label>
                            </div>
                            <div class="input-field col s12 m6 l4">
                                <select name="controlTypeId" id="controlTypeId">
                                    <option value="">Tous</option>
                                    <?php
                                    if (isset($data['controlTypes'])) {
                                        foreach ($data['controlTypes'] as $controlType) {
                                            $selected = isset($data['restrict']['controlType'])
                                                && $data['restrict']['controlType'] == $controlType->idControlType
                                                ? 'selected' : '';
                                            ?>
                                            <option value="<?= $controlType->idControlType ?>" <?= $selected ?>
                                                ><?= $controlType->controlTypeName ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="controlTypeId">Type de contrôle</label>
                            </div>
                        </div>
                        <div class="row col s12 m6 l4 right-align">
                            <button type="submit" class="btn">Filtrer</button>
                            <a class="btn" href="<?= base_url('Control') ?>">Remise à zéro</a>
                        </div>
                    </form>
                </div>
                <div class="horizontal-table-wrapper">
                    <table id="controls-table" class="highlight centered responsive-table">
                        <thead class="small-caps">
                            <tr>
                                <th>matière</th>
                                <th>libellé</th>
                                <th>groupe</th>
                                <th>type</th>
                                <th>coefficient</th>
                                <th>diviseur</th>
                                <th>ecart type</th>
                                <th>moyenne</th>
                                <th>date</th>
                                <th>edit.</th>
                                <th>notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($data['controls'])) {
                                foreach ($data['controls'] as $control) {
                                    $date = DateTime::createFromFormat('Y-m-d', $control->controlDate); ?>
                                    <tr>
                                        <td><?= $control->subjectCode . ' - ' . $control->subjectName ?></td>
                                        <td><?= $control->controlName ?></td>
                                        <td><?= $control->groupName ?></td>
                                        <td><?= $control->controlTypeName ?> </td>
                                        <td><?= (float) $control->coefficient ?></td>
                                        <td><?= (float) $control->divisor ?></td>
                                        <td><?= is_null($control->standardDeviation) ? 'Non calculée' : $control->standardDeviation ?></td>
                                        <td><?= is_null($control->average) ? 'Non calculée' : $control->average ?></td>
                                        <td><?= $date->format('d/m/Y') ?></td>
                                        <td>
                                            <a href="<?= base_url('Control/edit/' . $control->idControl) ?>">
                                                <i class="material-icons">edit</i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('Mark/add/' . $control->idControl) ?>">
                                                <i class="material-icons">note_add</i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <td colspan="99">Pas de contrôles</td>
                                <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-action">
                <a href="<?= base_url('Control/add') ?>" class="btn-flat waves-effect">Ajouter un controle</a>
                <a href="<?= base_url('Control/add/promo') ?>" class="btn-flat waves-effect">Ajouter un DS de promo</a>
            </div>
        </div>
    </main>
