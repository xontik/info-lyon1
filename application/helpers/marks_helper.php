<?php
	
	function afficherTableau($matiere, $coeff, $module)

	{
		
		$td = '<td style = "border: 0.5px solid grey; height: 2em; width: 30%;">&nbsp';
		$blanc = '<font color = "white">';
		
		return '<table style = "border-collapse: collapse; margin-bottom: 2em; width: 90%; margin-left: 5%; font-family: sans-serif; box-shadow: 1px 1px 2px #555;">

		   <tr style = "background-color: #4b4c4e;">' .
				
				
			   $td . $blanc . $matiere . '</font></td>' .

			   $td . $blanc . 'Coefficient : ' . $coeff . '</font></td>' .

			   $td . $blanc . 'Module : ' . $module . '</font></td>

		   </tr>
		   
		   <tr>' .

			   $td . 'Moyenne :</td>' .

			   $td . 'Médiane : </td>' .

			   $td . 'Classement : </td>

		   </tr>

		</table>';

	}

?>

	
