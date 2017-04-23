<main>
    <?php
    $mat = null;
    foreach ($data['controls'] as $control) {
        $date = date_create_from_format('Y-m-d',$control->dateControle);
        if($mat != $control->codeMatière){
            $mat = $control->codeMatière;
            echo "<p>".$control->nom."</p>";
        }
        echo "<p>Controle du ".date_format($date,'d/m/Y')." Groupe : ".$control->nomGroupe." Type : ".$control->type." Moyenne : ".$control->average." Medianne : ".$control->median."</p>";
    }
    ?>
</main>