<main class="container">
	<section>
        <h4>Questions</h4>
		<ul class="collapsible">
			<?php
			foreach ($data['questions'] as $question)
			{
                ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $question->title ?></div>
                        <div><?= $question->studentName ?></div>
                    </div>
                    <div class="collapsible-body">
                        <p><?= $question->content ?></p>
                        <?php
                        if (!empty($question->answers)) {
                            ?>
                            <ul>

                                <?php
                                foreach ($question->answers as $answer) {
                                    $isTeacher = $answer->teacher ? 'right-align' : '';
                                    ?>
                                    <li class="divider"></li>
                                    <li><p class="<?= $isTeacher ?>"><?= $answer->content ?></p></li>
                                    <?php
                                } ?>
                            </ul>

                            <?php
                        } ?>
                        <form action="<?= base_url('/Process_Question/answer/' . $question->idQuestion) ?>"
                              method="POST">
                            <div class="input-field">
                                <textarea class="materialize-textarea" name="text" id="text"></textarea>
                                <label for="text">Réponse</label>
                            </div>
                            <p>
                                <input type="checkbox" name="public" id="public">
                                <label for="public">Rendre publique</label>
                            </p>
                            <div class="btn-footer">
                                <button class="btn" type="submit">Envoyer</button>
                            </div>
                        </form>
                    </div>
                </li>
			    <?php
			} ?>
		</ul>
	</section>
</main>

