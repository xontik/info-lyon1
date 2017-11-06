<main class="container">
    <div class="section">
        <h4 class="header">Questions</h4>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['questions'] as $question)
            { ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $question->title ?></div>
                        <div>
                            <?= $question->teacherName
                            ?>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <p class="right-align"><?= $question->content ?></p>
                        <?php
                        if (!empty($question->answers)) {
                            ?>
                            <ul>
                                <?php
                                foreach ($question->answers as $answer) {
                                    $isTeacher = !$answer->teacher ? 'class="right-align"' : '';
                                    ?>
                                    <li class="divider"></li>
                                    <li><p <?= $isTeacher ?>><?= $answer->content ?></p></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                        <form action="<?= base_url('Process_Question/answer') ?>" method="POST">
                            <input type="hidden" name="idQuestion" value ="<?= $question->idQuestion ?>"/>
                            <div class="btn-footer">
                                <div class="input-field">
                                    <textarea class="materialize-textarea" name="text" id="text"></textarea>
                                    <label for="text">Réponse</label>
                                </div>
                                <button type="submit" class="waves-effect waves-light btn">Répondre</button>
                            </div>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Poser une question</span>
            <form action="<?= base_url('/Process_Question/send') ?>" method="POST">
                <div class="input-field">
                    <input type="text" name="title" id="title" autocomplete="off" data-length="255"/>
                    <label for="title">Titre</label>
                </div>
                <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="text" id="text"></textarea>
                    <label for="text">Question</label>
                </div>
                <div class="input-field row">
                    <select name="teacherId" id="teacherId" class="col s12 m8 l5">
                        <option value="" disabled selected>Choisir un professeur</option>
                        <?php
                        foreach ($data['teachers'] as $teacher)
                        { ?>
                            <option value="<?= $teacher->idTeacher?>"><?= $teacher->name ?></option>
                            <?php
                        } ?>
                    </select>
                    <label for="teacherId">Professeur</label>
                </div>
            </form>
        </div>
        <div class="card-action">
            <button type="submit" class="btn-flat waves-effect waves-light">Envoyer</button>
        </div>
    </div>
</main>
