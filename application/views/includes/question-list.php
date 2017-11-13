<div class="section">
    <?php
    if ($nbPages > 1) {
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
        foreach ($questions as $question) {
            ?>
            <li>
                <div class="collapsible-header">
                    <div><?= $question->title ?></div>
                    <div><?= $question->name ?></div>
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
    if ($nbPages > 1) {
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
