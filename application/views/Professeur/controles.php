<main>
    <p>
        <a href="<?php echo base_url('professeur/addControle')?>" >Ajouter un controle</a><br>
        <a href="<?php echo base_url('professeur/addControle/promo')?>" >Ajouter un DS de promo</a><br>

    </p>
    <?php
    $mat = null;
    if(isset($data['controls'][0])) {
        ?>
        <h2>Controles</h2>
        <table>
            <tr>
                <th>Matière</th>
                <th>Libélé</th>
                <th>Groupe</th>
                <th>Type</th>
                <th>Coeff</th>
                <th>Div</th>
                <th>Mediane</th>
                <th>Moyenne</th>
                <th>Date</th>
                <th>Supprimer</th>
                <th>Modifier</th>
                <th>Ajouter Note</th>
            </tr>

        <?php
        foreach ($data['controls'] as $control) {
            $date = date_create_from_format('Y-m-d', $control->dateControle);
            echo "<tr>";

            echo "<td>". $control->codeMatiere." - ".$control->nomMatiere . "</td>";
            echo "<td>". $control->nomControle . "</td>";
            echo "<td>". $control->nomGroupe . "</td>";
            echo "<td>". $control->typeControle . "</td>";
            echo "<td>". $control->coefficient . "</td>";
            echo "<td>". $control->diviseur . "</td>";
            echo "<td>". ($control->median != null ? ( $control->median) : "Non calculée") . "</td>";
            echo "<td>". ($control->average != null ? ($control->average) : "Non calculée") . "</td>";
            echo "<td>". $date->format("d/m/Y") . "</td>";
            echo "<td><a href='" . base_url("process_professeur/deletecontrole/" . $control->idControle) . "'>X</a></td>";
            echo "<td><a href='" . base_url("professeur/editcontrole/" . $control->idControle) . "'>Edit</a></td>";
            echo "<td><a href='" . base_url("professeur/ajoutNotes/" . $control->idControle) . "'>Notes</a></td>";
            echo "</tr>";

        }

        echo"</table>";
    }
    if(isset($data['dspromo'][0])){
        ?>
            <h2>DS de promo </h2>
            <table>
                <tr>
                    <th>Matière</th>
                    <th>Libellé</th>
                    <th>Div</th>
                    <th>Mediane</th>
                    <th>Moyenne</th>
                    <th>Date</th>
                    <th>Supprimer</th>
                    <th>Modifier</th>
                    <th>Ajouter Note</th>


                </tr>

        <?php
        $mat = null;
        foreach ($data['dspromo'] as $control) {
            echo"<tr>";
            $date = date_create_from_format('Y-m-d', $control->dateControle);

            echo "<td>". $control->codeMatiere." - ".$control->nomMatiere . "</td>";
            echo "<td>". $control->nomControle . "</td>";
            echo "<td>". $control->diviseur . "</td>";
            echo "<td>". ($control->median != null ? ( $control->median) : "Non calculée") . "</td>";
            echo "<td>". ($control->average != null ? ($control->average) : "Non calculée") . "</td>";
            echo "<td>". $date->format("d/m/Y") . "</td>";
            echo "<td><a href='" . base_url("process_professeur/deletecontrole/" . $control->idControle) . "'>X</a></td>";
            echo "<td><a href='" . base_url("professeur/editcontrole/" . $control->idControle) . "'>Edit</a></td>";
            echo "<td><a href='" . base_url("professeur/ajoutNotes/" . $control->idControle) . "'>Notes</a></td>";

            echo "</tr>";



        }
        echo "</table>";
    }
    ?>
</main>
