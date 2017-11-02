    <main class="container">
        <h4 class="header">Contrôles</h4>
        <div id="control-add" class="section">
            <a href="<?= base_url('Control/add') ?>" class="btn waves-effect">Ajouter un controle</a>
            <a href="<?= base_url('Control/add/promo') ?>" class="btn waves-effect">Ajouter un DS de promo</a>
        </div>
        <div class="card grey lighten-5">
            <div class="card-content">
                <div class="section col s12 m12 l10 offset-l1 no-pad-bot">
                    <form method="post" action="<?= base_url('Control')?>">
                        <div class="row valign-wrapper">
                            <div class="input-field col s12 m6 l4">
                                <select name="groupes" id="groupes">
                                    <option value="">Tous</option>
                                    <?php
                                    if (isset($data['groupes']) && count($data['groupes'])) {
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
                                    <option value="">Tous</option>
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
                                <label for="matieres">Matieres</label>
                            </div>
                            <div class="input-field col s12 m6 l4">
                                <select name="typeControle" id="typeControle">
                                    <option value="">Tous</option>
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
                            <a class="btn" href="<?= base_url('Control') ?>">Remise à zéro</a>
                        </div>
                    </form>
                </div>
                <table id="controls-table" class="highlight centered">
                    <thead class="small-caps">
                        <tr>
                            <th>matière</th>
                            <th>libellé</th>
                            <th>groupe</th>
                            <th>type</th>
                            <th>coefficient</th>
                            <th>diviseur</th>
                            <th>médiane</th>
                            <th>moyenne</th>
                            <th>date</th>
                            <th>suppr.</th>
                            <th>edit.</th>
                            <th>notes</th>
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
                                    <td><?= floatval($control->coefficient) ?></td>
                                    <td><?= floatval($control->diviseur) ?></td>
                                    <td><?= $control->median != null ? $control->median : 'Non calculée' ?> </td>
                                    <td><?= $control->average != null ? $control->average : 'Non calculée' ?>  </td>
                                    <td><?= $date->format('d/m/Y') ?></td>
                                    <td>
                                        <a href="<?= base_url('Process_Control/delete/' . $control->idControle) ?>">
                                            <i class="material-icons">delete</i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('Control/edit/' . $control->idControle) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('Mark/add/' . $control->idControle) ?>">
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
        </div>
    </main>
