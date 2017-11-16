<main class="container">
    <h4 class="header">Liste des étudiants</h4>
    <div class="section">
        <div class="input-field">
            <i class="material-icons prefix">account_circle</i>
            <input type="text" id="search" name="search" class="autocomplete">
            <label for="search">Rechercher un étudiant, un groupe...</label>
        </div>
    </div>
    <div class="section" id="list-student">
        <div class="row">
            <div class="sorter col s4" data-sort="idStudent">
                <b>Numéro étudiant</b>
                <i class="material-icons right scale-transition">keyboard_arrow_down</i>
            </div>
            <div class="sorter col s4" data-sort="surname">
                <b>Nom</b>
                <i class="material-icons right scale-transition scale-out">keyboard_arrow_down</i>
            </div>
            <div class="sorter col s4" data-sort="name">
                <b>Prénom</b>
                <i class="material-icons right scale-transition scale-out">keyboard_arrow_down</i>
            </div>
        </div>
        <div id="list-content"></div>
        <div id="list-progress" class="section">
            <div class="container progress">
                <div class="indeterminate"></div>
            </div>
        </div>
    </div>
</main>
