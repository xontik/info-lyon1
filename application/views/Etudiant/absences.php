<main>
    <table class="bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Horaire</th>
                <th>Type</th>
                <th>Justification</th>
            </tr>
        </thead>
  		<tbody>
          <?php
          if(!empty($data['absences'])) {
              foreach ($data['absences'] as $absence) { 
					$dateD = new DateTime($absence->dateDebut);
					$dateF = new DateTime($absence->dateFin);?>
                  <tr <?= ($absence->justifiee == 0)?'class="red-text text-accent-4"':'' ?>>
                    <td><?= $dateD->format('d/m/Y') ?></td>
                    <td><?= $dateD->format('H:i') .' &rarr; '. $dateF->format('H:i') ?></td>
                    <td><?= $absence->typeAbsence ?></td>
                    <td><?= ($absence->justifiee == 0)?'Injustifiée':'Justifiée' ?></td>
                  </tr>
              <?php }
          } else { ?>
              <tr>
                  <td colspan="99">Aucune absence</td>
              </tr>
          <?php } ?>
  		</tbody>
    </table>
</main>
   