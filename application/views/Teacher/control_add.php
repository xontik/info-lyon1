<main class="container">
    <h4 class="header">Ajouter un contrôle<?= $data['promo'] ? ' de promo' : '' ?></h4>
    <div class="card grey lighten-5">
        <form method="post"
            action="<?= base_url('Process_Control/add' . ($data['promo'] ? '/promo' : '')) ?>">
            <div class="card-content">
                <div class="row">
                    <div class="input-field col s12 l8">
                        <input type="text" id="name" name="name"/>
                        <label for="name">Libellé</label>
                    </div>
                    <div class="input-field col s12 m6 l4">
                        <select name="typeId" id="typeId">
                            <?php
                            foreach ($data['controlTypes'] as $controlType)
                            { ?>
                                <option value="<?= $controlType->idControlType ?>"
                                    <?= $data['promo'] && $controlType->idControlType == 1 ? 'selected' : '' ?>
                                    ><?= $controlType->controlTypeName ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <label for="typeId">Type de Controle</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6 m4 l1">
                        <input type="number" id="coefficient" name="coefficient" step="0.5" min="0" value="1"/>
                        <label for="coefficient">Coefficient</label>
                    </div>
                    <div class="input-field col s6 m4 l1">
                        <input type="number" id="divisor" name="divisor" min="1" value="20"/>
                        <label for="divisor">Diviseur</label>
                    </div>
                    <div class="input-field col s12 m6 l5">
                        <?php if ($data['promo'] === false)
                        { ?>
                            <select name="educationId" id="educationId">
                                <?php
                                foreach ($data['select'] as $education)
                                { ?>
                                    <option value="<?= $education->idEducation ?>"
                                        ><?= $education->groupName . $education->courseType . ' en ' . $education->subjectName ?>
                                    </option>
                                    <?php
                                } ?>
                            </select>
                            <label for="educationId">Groupe</label>
                            <?php
                        } else { ?>
                            <select name="subjectId" id="subjectId">
                                <?php
                                foreach ($data['select'] as $subject)
                                { ?>
                                    <option value="<?= $subject->idSubject ?>"
                                        ><?= $subject->subjectName ?>
                                    </option>
                                    <?php
                                } ?>
                            </select>
                            <label for="subjectId">Matière</label>
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
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect">Ajouter</button>
                <a class="btn-flat" href="<?= site_url('Control') ?>">Annuler</a>
            </div>
        </form>
    </div>
    <div id="modalPromo" class="modal container">
        <div class="modal-content flow-text center-align">
            <h4>Type de contrôle incompatibles</h4>
            <p>
                Vous avez changer de type de DS, et le type choisi est
                incompatible avec la page de création de DS actuelle.
            </p>
            <p>Voulez-vous être redirigé vers la page de création adaptée ?</p>
        </div>
        <div class="modal-footer btn-footer">
            <a id="promoRedirect" class="btn waves-effect">Oui</a>
            <a id="promoNoRedirect" class="btn waves-effect model-action modal-close">Non</a>
        </div>
    </div>
</main>
