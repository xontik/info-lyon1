<?php
if ($data['nbPages'] > 1) {
    $nbPages = $data['nbPages'];
    $currentPage = $data['currentPage'];
    $indexPagination = $data['indexPagination'];
    $limitPagination = $data['limitPagination'];
} ?>
<main class="container">
    <div class="section">
        <h4 class="header">Questions</h4>
        <?php
        if ($data['nbPages'] > 1) {
            ?>
            <div class="center-align">
                <ul class="pagination">
                    <?php
                    if ($currentPage != 1) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question') ?>">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage - 1)) ?>">
                                <i class="material-icons">chevron_left</i>
                            </a>
                        </li>
                        <?php
                    }

                    for ($i = $indexPagination; $i <= $indexPagination + $limitPagination - 1; $i++) {
                        if ($i == $nbPages + 1) {
                            break;
                        }
                        $isActive = ($i == $currentPage) ? 'active' : '';
                        ?>
                        <li class="waves-effect <?= $isActive ?>"><a href="<?= base_url("Question/$i") ?>"><?= $i ?></a></li>
                        <?php
                    }
                    if ($currentPage != $nbPages) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage + 1)) ?>">
                                <i class="material-icons">chevron_right</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . $nbPages) ?>">
                                <i class="material-icons">last_page</i>
                            </a>
                        </li>
                        <?php
                    } ?>
                </ul>
            </div>
            <?php
        } ?>
        <ul class="collapsible" data-collapsible="accordion">
            <?php
            foreach ($data['questions'] as $question) {
                ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $question->title ?></div>
                        <div><?= $question->teacherName ?></div>
                    </div>
                    <div class="collapsible-body">
                        <p class="right-align"><?= $question->content ?></p>
                        <?php
                        if (!empty($question->answers)) {
                            ?>
                            <ul>
                                <?php
                                foreach ($question->answers as $answer) {
                                    $isTeacher = !$answer->teacher ? 'right-align' : '';
                                    ?>
                                    <li class="divider"></li>
                                    <li><p class="<?= $isTeacher ?>"><?= $answer->content ?></p></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        } ?>
                        <form action="<?= base_url('Process_Question/answer/' . $question->idQuestion) ?>" method="POST">
                            <div class="input-field">
                                <textarea class="materialize-textarea" name="text" id="text"></textarea>
                                <label for="text">Réponse</label>
                            </div>
                            <button type="submit" class="btn-flat waves-effect waves-light">Répondre</button>
                        </form>
                    </div>
                </li>
                <?php
            } ?>
        </ul>
        <?php
        if ($data['nbPages'] > 1) {
            ?>
            <div class="center-align">
                <ul class="pagination">
                    <?php
                    if ($currentPage != 1) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question') ?>">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage - 1)) ?>">
                                <i class="material-icons">chevron_left</i>
                            </a>
                        </li>
                        <?php
                    }

                    for ($i = $indexPagination; $i <= $indexPagination + $limitPagination - 1; $i++) {
                        if ($i == $nbPages + 1) {
                            break;
                        }
                        $isActive = ($i == $currentPage) ? 'active' : '';
                        ?>
                        <li class="waves-effect <?= $isActive ?>"><a href="<?= base_url("Question/$i") ?>"><?= $i ?></a></li>
                        <?php
                    }

                    if ($currentPage != $nbPages) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage + 1)) ?>">
                                <i class="material-icons">chevron_right</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . $nbPages) ?>">
                                <i class="material-icons">last_page</i>
                            </a>
                        </li>
                        <?php
                    } ?>
                </ul>
            </div>
            <?php
        } ?>
    </div>
    <div class="card grey lighten-5">
        <form action="<?= base_url('/Process_Question/send') ?>" method="POST">
            <div class="card-content">
                <span class="card-title">Poser une question</span>
                <div class="input-field">
                    <input type="text" name="title" id="title" autocomplete="off" data-length="255"/>
                    <label for="title">Titre</label>
                </div>
                <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="text" id="text"></textarea>
                    <label for="text">Question</label>
                </div>
                <div class="input-field row no-margin">
                    <select name="teacherId" id="teacherId" class="col s12 m8 l5">
                        <option value="" disabled selected>Choisir un professeur</option>
                        <?php
                        foreach ($data['teachers'] as $teacher) {
                            ?>
                            <option value="<?= $teacher->idTeacher ?>"><?= $teacher->name ?></option>
                            <?php
                        } ?>
                    </select>
                    <label for="teacherId">Professeur</label>
                </div>
            </div>
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect waves-light">Envoyer</button>
            </div>
        </form>
    </div>
</main>
