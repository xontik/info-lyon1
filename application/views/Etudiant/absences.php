<main>
<?php
    if(!empty($absences)) {
        ?>
		<table id = "tableauAbs" >
			<tr style = "background-color: #4b4c4e;">
                <?
                $tdTete = '<td class = "lignesAbs"><span style = "margin-left: 10px;">';
                echo $tdTete .'Date</span></font></td>' .
                    $tdTete .'Type</span></font></td>' .
                    $tdTete .'Justification</span></font></td>';
                ?>
            </tr>
            <?
            $tdCorps = '<td class = "lignesAbs"><span style = "margin-left: 10px;">';
            foreach ($data["absences"] as $absence){
                $justifiee = $absence->justifiee;
                if($justifiee){
                    $j = 'Oui';
                }
                else{
                    $j = 'Non';
                }
                ?>
                <tr> <?
                    echo $tdCorps . $absence->dateDebut . '</span></td>' .
                        $tdCorps . $absence->typeAbsence . '</span></td>' .
                        $tdCorps . $j . '</span></td>';
                ?>
                </tr>
                <?
            }
            ?>
		</table>
		<?
	} else {
        echo "Pas d'absences";
    }
?>
</main>
