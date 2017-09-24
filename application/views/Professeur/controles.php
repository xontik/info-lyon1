    <main>
        <div id="control-add">
            <a href="<?= base_url('professeur/addControle') ?>">Ajouter un controle</a>
            <a href="<?= base_url('professeur/addControle/promo') ?>">Ajouter un DS de promo</a>
        </div>
        <?php
        if (count($data['controls']) > 0) {
            ?>
            <form method="post" action="/Professeur/Controle" id="filters">
                <?php
                // Group filter
                if (count($data['groupes']) > 1) {
                    echo '<div>';
                    echo '<h2>Groupes :</h2>';
                    foreach ($data['groupes'] as $groupe) {
                        $checked = '';
                        if (in_array($groupe->idGroupe,$data['restrict']['groupes'])) {
                            $checked = 'checked';
                        }
                        echo '<label id="choix" for="id'. $groupe->nomGroupe .'">'. $groupe->nomGroupe . $groupe->typeSemestre .'</label>'
                            . '<input type="checkbox" id="id'. $groupe->nomGroupe .'" name="'. $groupe->idGroupe .'" '.$checked.'>';
                    }
                    echo '</div>';
                }

                // Subject filter
                if (count($data['matieres'])) {
                    echo '<div>';
                    echo '<h2>Matieres :</h2>';
                    foreach ($data['matieres'] as $matiere) {
                        $checked = '';
                        if (in_array($matiere->codeMatiere,$data['restrict']['matieres'])) {
                            $checked = 'checked';
                        }
                        echo '<label id="choix" for="id'. $matiere->codeMatiere .'">'.$matiere->nomMatiere.'</label>'
                            . '<input type="checkbox" id="id'.$matiere->codeMatiere.'" name="'.$matiere->codeMatiere.'" '.$checked.' >';
                    }
                    echo '</div>';
                }

                // Control type filter
                echo '<div>';
                echo '<h2>Type de controles :</h2>';
                // Promos
                $checked  = '';
                if (in_array('DSPROMO',$data['restrict']['DS'])) {
                    $checked = 'checked';
                }
                echo '<label id="choix" for="idDSPROMO"> Ds Promo </label>'
                    . '<input type="checkbox" id="idDSPROMO" name="DSPROMO" '.$checked.'>';

                // Class tests
                $checked  = '';
                if (in_array('CC',$data['restrict']['DS'])) {
                    $checked = 'checked';
                }
                echo '<label id="choix" for="idCC"> CC </label>'
                    . '<input type="checkbox" id="idCC" name="CC" '.$checked.'>';

                echo '</div>';
                ?>
                <input type="submit" name="filter" value="Filter"/>
            </form>
            <?php
            $mat = null;

            ?>
            <table id="controls-table">
                <caption>Controles</caption>
                <thead>
                    <tr>
                        <td>matière</td>
                        <td>libélé</td>
                        <td>groupe</td>
                        <td>type</td>
                        <td>coeff</td>
                        <td>div</td>
                        <td>mediane</td>
                        <td>moyenne</td>
                        <td>date</td>
                        <td>suppr.</td>
                        <td>edit.</td>
                        <td>notes</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data['controls'] as $control) {
                        $date = DateTime::createFromFormat('Y-m-d', $control->dateControle);
                        echo '<tr>'
                        . '<td>' . $control->codeMatiere.' - '.$control->nomMatiere . '</td>'
                        . '<td>' . $control->nomControle . '</td>'
                        . '<td>' . $control->nomGroupe . '</td>'
                        . '<td>' . $control->typeControle . '</td>'
                        . '<td>' . $control->coefficient . '</td>'
                        . '<td>' . $control->diviseur . '</td>'
                        . '<td>' . ($control->median != null ? ( $control->median) : 'Non calculée') . '</td>'
                        . '<td>' . ($control->average != null ? ($control->average) : 'Non calculée') . '</td>'
                        . '<td>' . $date->format('d/m/Y') . '</td>'
                        . '<td>'
                            .'<a href="' . base_url('process_professeur/deletecontrole/' . $control->idControle) . '">'
                            . html_img('trash_delete.png', 'supprimer')
                            .'</a>'
                        . '</td>'
                        . '<td>'
                            . '<a href="' . base_url('professeur/editcontrole/' . $control->idControle) . '">'
                            . html_img('note_edit.png', 'modifier')
                            . '</a>'
                        . '</td>'
                        . '<td><a href="' . base_url('professeur/ajoutNotes/' . $control->idControle) . '">Notes</a></td>'
                        . '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php } ?>
    </main>
