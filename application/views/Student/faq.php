<main class="container">
    <h4 class="header">FAQ</h4>
    <?php 
    if (empty($data['questions'])) {
        ?>
        <div id="card-alert" class="card grey lighten-4">
            <div class="card-content">
                <p class="flow-text">Il n'y a aucune questions.</p>
            </div>
        <div>
        <?php
    } else {
        $questions = $data['questions'];
        ?>
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
                            }
                            ?>
                        </div>
                    </li>
                <?php }
                ?>
            </ul>
    <?php } ?>
</main>