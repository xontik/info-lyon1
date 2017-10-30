<?php
/**
* Created by PhpStorm.
* User: xontik
* Date: 24/04/2017
* Time: 00:33
*/
?>
<main class="container">
    <form method="post"
        action="<?= base_url('process_professeur/addcontrole/' . ($data['promo'] === true ? 'promo' : '')) ?>">
        <div class="section">
            <h4>Ajouter un contrôle</h4>
        </div>
        <div class="divider"></div>
        <div class="section">
            <div class="row">
                <div class="input-field col s12 l8">
                    <input type="text" id="nom" name="nom"/>
                    <label for="nom">Libellé</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="typeControle" id="typeControle">
                        <?php
                        foreach ($data['typeControle'] as $typeControle)
                        { ?>
                            <option value="<?= $typeControle->idTypeControle ?>"
                                <?= $data['promo'] === true && $typeControle->idTypeControle == '1' ? 'selected' : '' ?>
                            ><?= $typeControle->nomTypeControle ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <label for="typeControle">Type de Controle</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6 m4 l1">
                    <input type="number" id="coeff" name="coeff" step="0.5" min="0" value="1"/>
                    <label for="coeff">Coefficient</label>
                </div>
                <div class="input-field col s6 m4 l1">
                    <input type="number" id="diviseur" name="diviseur" min="1" value="20"/>
                    <label for="diviseur">Diviseur</label>
                </div>
                <div class="input-field col s12 m6 l5">
                    <?php if ($data['promo'] === false)
                    { ?>
                        <select name="enseignement" id="enseignement">
                            <?php
                            foreach ($data['select'] as $d)
                            { ?>
                                <option value="<?= $d->idEnseignement ?>"><?= $d->nomGroupe . ' en ' . $d->nomMatiere ?></option>
                                <?php
                            } ?>
                        </select>
                        <label for="enseignement">Groupe</label>
                        <?php
                    } else { ?>
                        <select name="matiere" id="matiere">
                            <?php
                            foreach ($data['select'] as $d)
                            { ?>
                                <option value="<?= $d->idMatiere ?>"><?= $d->codeMatiere . ' - ' . $d->nomMatiere ?></option>
                                <?php
                            } ?>
                        </select>
                        <label for="matiere">Matière</label>
                        <?php
                    } ?>
                </div>
                <div class="input-field col s12 m6 l5">
                    <input type="text" id="date" name="date"
                           class="datepicker" value="<?= date('d/m/Y') ?>"/>
                    <label for="date">Date du controle</label>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <div class="section btn-footer">
            <button type="submit" class="btn waves-effect">Ajouter</button>
            <a class="btn-flat" href="<?= site_url("professeur/controle") ?>">Annuler</a>
        </div>
    </form>
    <div id="modalPromo" class="modal container">
        <div class="modal-content flow-text center-align">
            <h4>Type de contrôle incompatibles</h4>
            <p>
                Vous avez changer de type de DS, et le type choisi est
                incompatible avec la page de création de DS actuelle.
            </p>
            <p>Voulez-vous être redirigé vers la page de création adaptée ?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="promoRedirect" class="btn waves-effect">Oui</a>
            <a href="#!" id="promoNoRedirect" class="btn waves-effect model-action modal-close">Non</a>
        </div>
    </div>
</main>
