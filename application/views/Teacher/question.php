<main class="container">
    <h4 class="header">Questions</h4>
    <form method="POST" action="<?= base_url('Question'); ?>">
        <div class="row">
            <div class="input-field col s12 m6 l3 offset-m6 offset-l9">
                <select onchange="this.form.submit()">
                    <option value="" disabled selected>Trier par groupe</option>
                    <?php foreach ($data['groups'] as $group) { ?>
                        <option value="<?= $group->idGroup ?>"><?= $group->groupName . $group->courseType ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </form>
    <?= $data['questionList'] ?>
</main>
