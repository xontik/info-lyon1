<main class="container">
    <h4 class="header">Liste des étudiants</h4>
    <div class="section">
        <div class="input-field">
            <i class="material-icons prefix">account_circle</i>
            <input type="text" id="search" name="search" class="autocomplete">
            <label for="search">Rechercher un étudiant</label>
        </div>
    </div>
    <?= $data['studentList'] ?>
</main>