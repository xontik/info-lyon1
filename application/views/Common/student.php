<main class="container">
    <h4 class="header">Liste des étudiants</h4>
    <div class="section">
        <div class="input-field">
            <i class="material-icons prefix">account_circle</i>
            <input type="text" id="search" name="search" class="autocomplete">
            <label for="search">Rechercher un étudiant</label>
        </div>
    </div>
    <div class="section">
        <table class="striped">
            <thead>
                <tr>
                    <th>Numéro d'étudiant</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                </tr>
            </thead>
            <tbody id="list-content">
            </tbody>
        </table>
        <div id="list-progress" class="section">
            <div class="container progress">
                <div class="indeterminate"></div>
            </div>
        </div>
    </div>
</main>
