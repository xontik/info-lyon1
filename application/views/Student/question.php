<main class="container">
    <div class="section">
        <h4 class="header">Questions</h4>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['questions'] as $question) {
                ?>
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
                        foreach ($data['teachers'] as $teacher) {
                            ?>
                            <option value="<?= $teacher->idTeacher ?>"><?= $teacher->name ?></option>
                        <?php }
                        ?>
                    </select>
                    <label for="teacherId">Professeur</label>
                </div>
                <div class="btn-footer">
                    <button type="submit" class="waves-effect waves-light btn">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
    <?php if ($data['nbPages'] > 1) { ?>
        <center>
            <?php
            $nbPages = $data['nbPages'];
            $currentPage = $data['currentPage'];
            $leftIsDisabled = ($currentPage == 1);
            $rightIsDisabled = ($currentPage == $nbPages);
            ?>
            <ul class="pagination">
                <li class="<?= $leftIsDisabled ? 'disabled' : 'waves-effect' ?>">
                    <a <?= $leftIsDisabled ? '' : 'href="' . base_url("Question/") . ($currentPage - 1) . '"' ?>>
                        <i class="material-icons">chevron_left</i>
                    </a>
                </li>
                <?php
                $indexPagination = 1;
                $limitPagination = 9;
                $changePaginationNumber = 5;
                if ($currentPage > $changePaginationNumber) {
                    if ($currentPage <= $nbPages - $changePaginationNumber) {
                        $indexPagination = 1 + ($currentPage - $changePaginationNumber);
                    } else {
                        if ($currentPage > $limitPagination) {
                            $indexPagination = 1 + ($nbPages - $limitPagination);
                        }
                    }
                }
                for ($i = $indexPagination; $i <= $indexPagination + $limitPagination - 1; $i++) {
                    if ($i == $nbPages + 1) {
                        break;
                    }
                    $isActive = ($i == $currentPage) ? 'active' : '';
                    echo '<li class="waves-effect ' . $isActive . '"><a href="' . base_url("Question/$i") . '">' . $i . '</a></li>';
                }
                ?>
                <li class="<?= $rightIsDisabled ? 'disabled' : 'waves-effect' ?>">
                    <a <?= $rightIsDisabled ? '' : 'href="' . base_url("Question/") . ($currentPage + 1) . '"' ?>>
                        <i class="material-icons">chevron_right</i>
                    </a>
                </li>
            </ul>
        </center>
    <?php } ?>
</main>