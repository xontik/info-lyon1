<main class="container">
    <h4 class="header">Absences</h4>
    <table class="bordered centered">
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
                    $dateF = new DateTime($absence->dateFin); ?>
                    <tr <?= !$absence->justifiee ? 'class="red-text text-accent-4"' : '' ?>>
                        <td><?= $dateD->format('d/m/Y') ?></td>
                        <td>
                            <?= $dateD->format('H:i') ?>
                            <i class="material-icons">arrow_forward</i>
                            <?= $dateF->format('H:i') ?>
                        </td>
                        <td><?= $absence->typeAbsence ?></td>
                        <td><?= !$absence->justifiee ? 'Injustifiée' : 'Justifiée' ?></td>
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
   