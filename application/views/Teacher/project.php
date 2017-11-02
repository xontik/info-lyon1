<main>
    <h4>Projets Tuteurés</h4>
    <?php
    foreach ($data['ptuts'] as $ptut)
    { ?>
        <p><a href="<?= base_url('/Project/detail/' . $ptut->idGroupe) ?>"><?= $ptut->nomGroupe?></a></p>
        <?php
    }
    ?>
</main>
