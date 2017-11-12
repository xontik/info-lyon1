<main class="container">
    <h4 class="header">Liste des étudiants</h4>
    <div class="card grey lighten-5">
        <div class="card-content">
            <div class="card-title">Recherche</div>
            <div class="input-field">
                <i class="material-icons prefix">account_circle</i>
                <input type="text" id="search" name="search" class="autocomplete">
                <label for="search"></label>
            </div>
        </div>
    </div>
    <?= $data['studentList'] ?>
</main>