<?php 
if (empty($questions)) {
    ?>
    <div id="card-alert" class="card grey lighten-4">
        <div class="card-content">
            <p class="flow-text">Vous n'avez pas posé de question</p>
        </div>
    <div>
    <?php
} else {
    ?>
    <div class="card grey lighten-5">
        <form action="<?= base_url('Question') ?>" method="GET">
            <div class="card-content">
                <div class="input-field">
                    <i class="material-icons prefix">search</i>
                    <input name="s" class="grey lighten-5" id="search" type="search" required>
                    <label for="search">Rechercher une question</label>
                </div>
            </div>
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect waves-light">Rechercher</button>
                <a href="<?= base_url('Question') ?>" class="btn-flat waves-effect waves-light">Réinitialiser</a>
            </div>
        </form>
    </div>
    <div class="section">
        <?php
        $searchLink = $search ? "?s=$search" : '';
        if ($nbPages > 1) {
            ?>
            <div class="center-align">
                <ul class="pagination">
                    <?php
                    if ($currentPage != 1) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question/1' . $searchLink) ?>">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage - 1) . $searchLink) ?>">
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
                        <li class="waves-effect <?= $isActive ?>"><a href="<?= base_url("Question/$i". $searchLink) ?>"><?= $i ?></a></li>
                        <?php
                    }
                    if ($currentPage != $nbPages) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage + 1) . $searchLink) ?>">
                                <i class="material-icons">chevron_right</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . $nbPages . $searchLink) ?>">
                                <i class="material-icons">last_page</i>
                            </a>
                        </li>
                    <?php }
                    ?>
                </ul>
            </div>
        <?php }
        ?>
        <ul class="collapsible" data-collapsible="accordion">
            <?php
            foreach ($questions as $question) {
                $active = $activeQuestion == $question->idQuestion
                    ? 'active' : '';
                ?>
                <li>
                    <div class="collapsible-header <?= $active ?>">
                        <div><?= $question->title ?></div>
                        <div><?= $question->name ?></div>
                    </div>
                    <div class="collapsible-body">
                        <p <?= $teacher ? 'class="right-align"' : '' ?>><?= $question->content ?></p>
                        <?php
                        if (!empty($question->answers)) {
                            ?>
                            <ul>
                                <?php
                                foreach ($question->answers as $answer) {
                                    $isRight = $teacher
                                        ? ($answer->teacher ? '' : 'right-align')
                                        : ($answer->teacher ? 'right-align' : '');
                                    ?>
                                    <li class="divider"></li>
                                    <li><p class="<?= $isRight?>"><?= $answer->content ?></p></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        if ($teacher) {
                            ?>
                            <div class="switch">
                                <form id="<?= $question->idQuestion ?>"
                                      action ="<?= base_url('Process_Question/set_public/' . $question->idQuestion); ?>"
                                      method="post">
                                    <label>
                                        <input
                                            name="checkPublic"
                                            type="checkbox"
                                            <?= $question->public ? 'checked' : '' ?>
                                            onchange="document.getElementById('<?= $question->idQuestion ?>').submit();"
                                        >
                                        <span class="lever"></span>
                                        Publique
                                    </label>
                                </form>
                            </div>
                            <?php
                        }
                        ?>
                        <form action="<?= base_url('Process_Question/answer/' . $question->idQuestion) ?>" method="POST">
                            <div class="input-field">
                                <textarea class="materialize-textarea" name="text" id="text"></textarea>
                                <label for="text">Réponse</label>
                            </div>
                            <button type="submit" class="btn-flat waves-effect waves-light">Répondre</button>
                        </form>
                    </div>
                </li>
            <?php }
            ?>
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
                            <a href="<?= base_url('Question' . $searchLink) ?>">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage - 1) . $searchLink) ?>">
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
                        <li class="waves-effect <?= $isActive ?>"><a href="<?= base_url("Question/$i". $searchLink)?>"><?= $i ?></a></li>
                        <?php
                    }

                    if ($currentPage != $nbPages) {
                        ?>
                        <li>
                            <a href="<?= base_url('Question/' . ($currentPage + 1) . $searchLink) ?>">
                                <i class="material-icons">chevron_right</i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('Question/' . $nbPages . $searchLink) ?>">
                                <i class="material-icons">last_page</i>
                            </a>
                        </li>
                    <?php }
                    ?>
                </ul>
            </div>
        <?php }
        ?>
    </div>
<?php } ?>
