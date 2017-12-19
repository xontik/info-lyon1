<main class="container">
    <div class="card grey lighten-5">
        <form action="<?= base_url('Process_Profile/change_password') ?>" method="POST">
            <div class="card-content">
                <span class="card-title">Modifier votre mot de passe</span>
                <div class="input-field">
                    <label for="formerPassword">Ancien mot de passe</label>
                    <input type="password" name="formerPassword" id="formerPassword">
                </div>
                <div class="input-field">
                    <label for="newPassword">Nouveau mot de passe</label>
                    <input type="password" name="newPassword" id="newPassword">
                </div>
                <div class="input-field">
                    <label for="confirmPassword">Confirmer</label>
                    <input type="password" name="confirmPassword" id="confirmPassword">
                </div>
            </div>
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect waves-light">Envoyer</button>
            </div>
        </form>
    </div>
</main>
