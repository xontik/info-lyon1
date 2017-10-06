<main>
	<section>
		<header>Les questions</header>
		<ul>
			<?php
			foreach ($data['profQuestions'] as $profQuestion) {?>
				<div>
					<?php $student = $this->studentMod->getStudent($profQuestion->numEtudiant);?>
					<li class = "question qr">
						<?php echo $profQuestion->texte;?>
						<span><?php echo $profQuestion->numEtudiant . ' - ' . $student->prenom . ' ' . strtoupper($student->nom);?></span>
					</li>
					<ul>
						<?php
						$listeReponses = $this->repMod->getAnswers($profQuestion->idQuestion);
						foreach($listeReponses as $reponse){
							echo '<li class="reponse qr">'. $reponse->texte. '</li>';
						} 
						?>
						<form action="<?php echo current_url();?>" method="POST">
							<input type="hidden" name="idQuestion" value ="<?php echo $profQuestion->idQuestion;?>"/>
							<input type="text" name="texte" autocomplete = "off"/>
							<input type="submit" value = "Répondre" />
						</form>
					</ul>
				</div>
			<?php
			}
			?>
		</ul>
	</section>
</main>

