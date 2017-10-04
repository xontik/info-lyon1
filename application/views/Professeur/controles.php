    <main>
        <div id="control-add">
            <a href="<?= base_url('professeur/addControle') ?>">Ajouter un controle</a>
            <a href="<?= base_url('professeur/addControle/promo') ?>">Ajouter un DS de promo</a>
        </div>

  <?php
  if(isset($data['controls'])) {

    ?>
  <div>
  <form method="post" action="<?php echo base_url('professeur/controle')?>">
    <?php
    if(count($data['groupes'])){?>
      <label for="">Groupes : </label>
        <select name="groupes" id="groupes">
          <option value="0">Tous</option>
      <?php
      foreach ($data['groupes'] as $groupe) {
        $selected = "";
        if(isset($data["restrict"]["groupes"]) && $groupe->idGroupe == $data["restrict"]["groupes"]){
          $selected = "selected";
        }
          echo '<option value="'.$groupe->idGroupe.'" '.$selected.' >'.$groupe->nomGroupe.$groupe->type.'</option>'.PHP_EOL;
      }
      ?>
      </select>
      <?php

    }
    if(isset($data['matieres'])){
      ?>
      <label for="">Matieres : </label>
        <select name="matieres" id="matieres">
          <option value="0">Tous</option>

      <?php
      foreach ($data['matieres'] as $matiere) {
        $selected = "";
        if(isset($data["restrict"]["matieres"]) && $matiere->idMatiere == $data["restrict"]["matieres"]){
          $selected = "selected";
        }
          echo '<option value="'.$matiere->idMatiere.'" '.$selected.'>'.$matiere->codeMatiere.' - '.$matiere->nomMatiere.'</option>'.PHP_EOL;
      }
      ?>
      </select>
      <?php

    }

    if(isset($data['typeControle'])){
      ?>
      <label for="">Type de Controle : </label>

        <select name="typeControle" id="typeControle">
          <option value="0">Tous</option>

      <?php
      foreach ($data['typeControle'] as $typeControle) {
        $selected = "";
        if(isset($data["restrict"]["typeControle"]) && $typeControle->idTypeControle == $data["restrict"]["typeControle"]){
          $selected = "selected";
        }
          echo '<option value="'.$typeControle->idTypeControle.'" '.$selected.'>'.$typeControle->nomTypeControle.'</option>'.PHP_EOL;
      }
      ?>
      </select>
      <?php

    }



    ?>

    <input type="submit" name="filter" value="Filter"/>
    <a href="<?= base_url('professeur/controle') ?>">Reset all</a>
  </form>
</div>
  <?php
  $mat = null;
      if(count($data['controls'])){
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
                        . '<td>' . $control->nomTypeControle . '</td>'
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
        <?php }
      }
      ?>
    </main>
