    <main class="container">
        <div id="control-add" class="section">
            <a href="<?= base_url('professeur/addControle') ?>" class="btn waves-effect">Ajouter un controle</a>
            <a href="<?= base_url('professeur/addControle/promo') ?>" class="btn waves-effect">Ajouter un DS de promo</a>
        </div>
        <div class="divider"></div>
        <?php
        if (isset($data['controls'])) { ?>
            <div class="section col s12 m12 l10 offset-l1 no-pad-bot">
                <form method="post" action="<?php echo base_url('professeur/controle')?>">
                    <div class="row valign-wrapper">
                        <div class="input-field col s12 m6 l4">
                            <select name="groupes" id="groupes">
                                <option value="0">Tous</option>
                                <?php
                                if (count($data['groupes'])) {
                                    foreach ($data['groupes'] as $groupe) {
                                        $selected = isset($data['restrict']['groupes'])
                                            && $data['restrict']['groupes'] == $groupe->idGroupe
                                            ? 'selected' : '';
                                        echo '<option value="' . $groupe->idGroupe . '" ' . $selected . ' >'
                                            . $groupe->nomGroupe . $groupe->type
                                            . '</option>' . PHP_EOL;
                                    }
                                }
                                ?>
                            </select>
                            <label for="groupes">Groupe</label>
                        </div>
                        <div class="input-field col s12 m6 l4">
                            <select name="matieres" id="matieres">
                                <option value="0">Tous</option>
                                <?php
                                if (isset($data['matieres'])) {
                                    foreach ($data['matieres'] as $matiere) {
                                        $selected = isset($data['restrict']['matieres'])
                                        && $matiere->idMatiere == $data['restrict']['matieres']
                                            ? 'selected' : '';
                                        echo '<option value="' . $matiere->idMatiere . '" ' . $selected . '>'
                                            . $matiere->codeMatiere . ' - ' . $matiere->nomMatiere
                                            . '</option>' . PHP_EOL;
                                    }
                                }
                                ?>
                            </select>
                            <label for="matieres">Matieres : </label>
                        </div>
                        <div class="input-field col s12 m6 l4">
                            <select name="typeControle" id="typeControle">
                                <option value="0">Tous</option>
                                <?php
                                if (isset($data['typeControle'])) {
                                    foreach ($data['typeControle'] as $typeControle) {
                                        $selected = isset($data['restrict']['typeControle'])
                                            && $typeControle->idTypeControle == $data['restrict']['typeControle']
                                            ? 'selected' : '';
                                        echo '<option value="' . $typeControle->idTypeControle . '" ' . $selected . '>'
                                            . $typeControle->nomTypeControle
                                            . '</option>' . PHP_EOL;
                                    }
                                }
                                ?>
                            </select>
                            <label for="typeControle">Type de contrôle</label>
                        </div>
                    </div>
                    <div class="row col s12 m6 l4 right-align">
                        <button type="submit" class="btn">Filtrer</button>
                        <a class="flat-btn" href="<?= base_url('professeur/controle') ?>">Remise à zéro</a>
                    </div>
                </form>
            </div>
            <div class="divider"></div>
            <div class="section">
                <table id="controls-table">
                    <thead class="small-caps">
                        <tr>
                            <td>matière</td>
                            <td>libellé</td>
                            <td>groupe</td>
                            <td>type</td>
                            <td>coefficient</td>
                            <td>diviseur</td>
                            <td>médiane</td>
                            <td>moyenne</td>
                            <td>date</td>
                            <td>suppr.</td>
                            <td>edit.</td>
                            <td>notes</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data['controls'])) {
                            foreach ($data['controls'] as $control) {
                                $date = DateTime::createFromFormat('Y-m-d', $control->dateControle); ?>
                                <tr>
                                    <td><?= $control->codeMatiere . '-' . $control->nomMatiere ?></td>
                                    <td><?= $control->nomControle ?></td>
                                    <td><?= $control->nomGroupe ?></td>
                                    <td><?= $control->nomTypeControle ?> </td>
                                    <td><?= $control->coefficient ?></td>
                                    <td><?= $control->diviseur ?></td>
                                    <td><?= $control->median != null ? $control->median : 'Non calculée' ?> </td>
                                    <td><?= $control->average != null ? $control->average : 'Non calculée' ?>  </td>
                                    <td><?= $date->format('d/m/Y') ?></td>
                                    <td class="center-align">
                                        <a href="<?= base_url('process_professeur/deletecontrole/' . $control->idControle) ?>">
                                            <i class="material-icons">delete</i>
                                        </a>
                                    </td>
                                    <td class="center-align">
                                        <a href="<?= base_url('professeur/editcontrole/' . $control->idControle) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                    </td>
                                    <td class="center-align">
                                        <a href="<?= base_url('professeur/ajoutNotes/' . $control->idControle) ?>">
                                            <i class="material-icons">note_add</i>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <td colspan="99">Pas de contrôles</td>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </main>
