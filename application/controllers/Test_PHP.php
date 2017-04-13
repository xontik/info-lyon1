
<?php

class Test_PHP{
	
	public function pages($p1){
		echo "<p>";
		$i = 0;
		$esp = "";
		while($i != $p1 + 1){
			echo $i . "<br>" . $esp;
			$i++;
			$esp = $esp . "&nbsp";
		}		
		echo "</p>";
		
	}
	
	

	
	
}












