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
                    $beginDate = new DateTime($absence->beginDate);
                    $endDate = new DateTime($absence->endDate); ?>
                    <tr <?= !$absence->justified ? 'class="red-text text-accent-4"' : '' ?>>
                        <td><?= $beginDate->format('d/m/Y') ?></td>
                        <td>
                            <?= $beginDate->format('H:i') ?>
                            <i class="material-icons">arrow_forward</i>
                            <?= $endDate->format('H:i') ?>
                        </td>
                        <td><?= $absence->absenceTypeName ?></td>
                        <td><?= $absence->justified ? 'Justifiée' : 'Injustifiée' ?></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td colspan="99">Aucune absence</td>
                </tr>
                <?php
            } ?>
        </tbody>
    </table>
</main>
   