<main>
	<section>
		<header>Les questions</header>
		<?php
		foreach ($data['profQuestions'] as $profQuestion) {
			$student = $this->studentMod->getStudent($profQuestion->numEtudiant);?>
			<div><?php echo $profQuestion->numEtudiant . ' - ' . $student->prenom . ' ' . strtoupper($student->nom);?></div>
			<div><?php echo $profQuestion->texte; ?></div>
		<?php
			$listeReponses = $this->repMod->getAnswers($profQuestion->idQuestion);
			foreach($listeReponses as $reponse){
				echo '<p>'. $reponse->texte. '</p>';
			} ?>
			<form action="<?php echo current_url();?>" method="POST">
				<input type="hidden" name="idQuestion" value ="<?php echo $profQuestion->idQuestion;?>"/>
				<input type="text" name="texte" />
				<input type="submit" value = "Répondre" />
			</form>
			<?php
		}
		?>
	</section>
</main>

