    <main>

      <section id="tree">
      <h2>Liste des parcours exsitants</h2>
      <input type="button" name="expand"  id="expand" value="Tout dérouler">
      <input type="button" name="restrain"  id="restrain" value="Tout enrouler">

      <ul id="ulroot">
      <?php
        foreach ($data['parcours'] as $idParcours => $parcours) {

          echo '<li class="depliant">'.$parcours['type'].' demarre en '.$parcours['anneeCreation'].'-'.((int)($parcours['anneeCreation'])+1);
          
          echo '<ul >';
          foreach ($parcours['UEs'] as $idUE => $UEs) {

            echo '<li class="depliant"><strong>' .$UEs['codeUE'].'</strong> '.$UEs['nomUE'] . ' <strong>Coeff.' . $UEs['coefficientUE'].'</strong>';
            if($parcours['editable']){

            }
            echo '<ul>';
            foreach ($UEs['Modules'] as $idModule => $modules) {
              $depliant = (count($modules['matieres'])>1)?' depliant':'';

              echo '<li class="' .$depliant.'" ><strong>' .$modules['codeModule'].'</strong> '.$modules['nomModule'] . ' <strong>Coeff.' . $modules['coefficientModule'].'</strong>';
              if($parcours['editable']){

              }
              if(count($modules['matieres']) > 1){

                echo '<ul>';

                foreach ($modules['matieres'] as $idMatiere => $matieres) {
                  if($matieres['nomMatiere'] != null){
                    echo '<li ><strong>' .$matieres['codeMatiere'].'</strong> '.$matieres['nomMatiere'].' <strong>Coeff.' .$matieres['coefficientMatiere'].'</strong></li>';
                  }


                }
                echo '</ul>';
              }
              echo '</li>';
            }
            echo '</ul></li>';
          }
          echo '</ul></li>';
        }
       ?>
     </ul>
   </section>
    </main>
