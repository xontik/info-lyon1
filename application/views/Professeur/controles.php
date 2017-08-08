<main>
    <p>
        <a href="<?php echo base_url('professeur/addControle')?>" >Ajouter un controle</a><br>
        <a href="<?php echo base_url('professeur/addControle/promo')?>" >Ajouter un DS de promo</a><br>

    </p>
    <?php
    $mat = null;
    if(isset($data['controls'][0])) {
        echo "<h2>Controles</h2>";

        foreach ($data['controls'] as $control) {
            $date = date_create_from_format('Y-m-d', $control->dateControle);
            if ($mat != $control->codeMatiere) {
                $mat = $control->codeMatiere;
                echo "<p>" . $control->nomMatiere . "</p>";
            }
            echo "<p>Controle du " . date_format($date, 'd/m/Y') . " Nom : ".$control->nomControle." Groupe : " . $control->nomGroupe . " Type : " . $control->typeControle . ($control->average != null ? ( " Moyenne : " . $control->average) : "") . ($control->median != null ? (" Medianne : " . $control->median): "") . " 
            <a href='" . base_url("process_professeur/deletecontrole/" . $control->idControle) . "'>X</a>
            <a href='" . base_url("professeur/editcontrole/" . $control->idControle) . "'>Edit</a></p>";
        }
    }
    if(isset($data['dspromo'])){
        echo "<h2>DS de promo </h2>";
        $mat = null;
        foreach ($data['dspromo'] as $control) {
            $date = date_create_from_format('Y-m-d', $control->dateControle);
            if ($mat != $control->codeMatiere) {
                $mat = $control->codeMatiere;
                echo "<p>" . $control->nomMatiere . "</p>";
            }
            echo "<p>Controle du " . date_format($date, 'd/m/Y') ." Nom : ".$control->nomControle."  Type : " . $control->typeControle . ($control->average != null ? ( " Moyenne : " . $control->average) : "") . ($control->median != null ? (" Medianne : " . $control->median): "") . " 
            <a href='" . base_url("process_professeur/deletecontrole/" . $control->idControle) . "'>X</a>
            <a href='" . base_url("professeur/editcontrole/" . $control->idControle) . "'>Edit</a></p>";
        }
    }
    ?>
</main>
