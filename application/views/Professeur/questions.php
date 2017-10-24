<main>
	<section>
		<header>Les questions</header>
		<ul>
			<?php
			foreach ($data['profQuestions'] as $profQuestion) {?>
				<div>
					<?php $student = $this->studentMod->getStudent($profQuestion->numEtudiant);?>
					<li class = "question qr">
						<div>
							<div>
							<?php 
								$i=0; 
								while(!($i>=30 OR $i==strlen($profQuestion->texte))){
									if(ord($profQuestion->texte[$i])<=127){echo $profQuestion->texte[$i];$i++;}
									else{echo $profQuestion->texte[$i].$profQuestion->texte[$i+1];$i=$i+2;}
								}
								if($i>=30 AND $i <strlen($profQuestion->texte)){echo '...';}?>
							</div>
							<div><?php echo $profQuestion->numEtudiant . ' - ' 
							. $student->prenom . ' ' . strtoupper($student->nom);?></div>
						</div>
					</li>
					<ul>
						<?php
						$listeReponses = $this->questionsMod->getAnswers($profQuestion->idQuestion);
						foreach($listeReponses as $reponse){
							$estProf='';
							if($reponse->prof==1){$estProf='isProf';}
							echo '<li class="qr '. $estProf .'">'. $reponse->texte. '</li>';
						} 
						?>
						<form action="/Process_professeur/repondreQuestion" method="POST">
							<input type="hidden" name="idQuestion" value ="<?php echo $profQuestion->idQuestion;?>"/>
							<div>
								<input type="text" name="texte" autocomplete = "off"/>
								<input type="submit" value = "Répondre" />
							</div>
						</form>
					</ul>
				</div>
			<?php
			}
			?>
		</ul>
	</section>
</main>

