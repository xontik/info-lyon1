    <main>
        <table id="tableauAbs" >
            <thead>
                <td>Date</td>
                <td>Type</td>
                <td>Justification</td>
            </thead>
            <?php
            if(!empty($data['absences'])) {
                foreach ($data['absences'] as $absence) { ?>
                    <tr>
                        <td><?php echo $absence->dateDebut; ?></td>
                        <td><?php echo $absence->typeAbsence; ?></td>
                        <td><?php echo $absence->justifiee ? 'Oui' : 'Non' ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="99">Pas d'absences</td>
                </tr>
            <?php } ?>
        </table>
    </main>
