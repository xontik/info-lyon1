<<<<<<< Updated upstream
    <main>
    </main>
=======
<main>
	<section>
		<header>Les questions</header>
		<?php
		foreach ($data['profQuestions'] as $profQuestion) {
			$student = $this->studentMod->getStudent($profQuestion->numEtudiant);?>
			<div><?php echo $profQuestion->numEtudiant . ' - ' . $student->prenom . ' ' . strtoupper($student->nom);?></div>
			<div><?php echo $profQuestion->corps; ?></div>
		<?php
		}
		?>
	</section>
</main>
>>>>>>> Stashed changes
