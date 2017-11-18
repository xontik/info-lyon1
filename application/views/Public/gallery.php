<main class="container" >
	<div class="carousel carousel-slider">
		<?php foreach ($data['projects'] as $project){
			echo '<a class="carousel-item"><img src="/assets/images/projects/'.$project->projectPicture.'"></a>';
		}
		?>
	</div>
</main>