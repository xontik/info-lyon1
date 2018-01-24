<main class="container">
    <h4 class="header">Questions</h4>
    <?= $data['questionList'] ?>
    <div class="card grey lighten-5">
        <form action="<?= base_url('/Process_Question/send') ?>" method="POST">
            <div class="card-content">
                <span class="card-title">Poser une question</span>
                <div class="input-field">
                    <input type="text" name="title" id="title" autocomplete="off" data-length="255" required/>
                    <label for="title">Titre</label>
                </div>
                <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="text" id="text" required></textarea>
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
