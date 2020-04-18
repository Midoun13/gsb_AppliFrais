<?php
/**
 * Vue Liste des visiteurs 
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    Nabil MIDOUN <nabil.midoun@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
if (!$estFicheValidee) {
    if ($typeUtilisateur == 'comptable') { ?>
    <form 
        <?php if ($uc == 'validerFrais') { 
            ?> action="index.php?uc=validerFrais&action=afficherFrais" 
        <?php } elseif ($uc == 'suivreFrais') {
            
            ?> action="index.php?uc=suivreFrais&action=voirEtatFrais" <?php 
        } ?>
              method="post" role="form" class="choix-comptable">
        <div class="choix-fiche">
            <label class="label-visiteur" for="lstVisiteur" accesskey="n">
            Choisir le visiteur : </label>
            <select id="lstVisiteur" name="lstVisiteur" class="form-control">
            <?php 
            foreach ($lesVisiteurs as $unVisiteur) { 
                $id = htmlspecialchars($unVisiteur['id']);
                $nom = htmlspecialchars($unVisiteur['nom']);
                $prenom = htmlspecialchars($unVisiteur['prenom']);
                if (isset($_SESSION['idVisiteurSelectionne']) 
                    && $id == $_SESSION['idVisiteurSelectionne']
                ) {
                    ?>
                <option selected value = "<?php echo $id ?>">
                    <?php echo $nom . ' ' . $prenom ?> </option>
                    <?php
                } else {
                    ?>
                <option value="<?php echo $id ?>">
                    <?php echo $nom . ' ' . $prenom ?> </option>
                    <?php 
                }
            } 
            ?>
            </select>
        </div>
        <div class="choix-fiche">
            <label class="label-mois" for="lstMois" accesskey="n">Mois : </label>
            <select id="lstMois" name="lstMois" class="form-control">
            <?php
            foreach ($lesMois as $unMois) {
                $mois = $unMois['mois'];
                $numAnnee = $unMois['numAnnee'];
                $numMois = $unMois['numMois'];
                if (isset($_SESSION['moisSelectionne']) 
                    && $mois == $_SESSION['moisSelectionne']
                ) {
                    ?>
                    <option selected value="<?php echo $mois ?>">
                        <?php echo $numMois . '/' . $numAnnee ?> </option><?php
                } else {
                    ?>
                    <option value="<?php echo $mois ?>">
                        <?php echo $numMois . '/' . $numAnnee ?> </option><?php
                }
            }
            ?>    
            </select>
        </div>
        <input id="ok" type="submit" value="Valider" class="btn btn-success" 
               role="button">
    </form>
    <?php }
} if ($estFicheValidee) { ?>
    <p class="alert alert-success">La fiche du 
    <?php echo $moisAnnee ?> pour le visiteur 
    <?php echo htmlspecialchars($nomEtPrenomVisiteur['nom']) . ' '
               . htmlspecialchars($nomEtPrenomVisiteur['prenom']) ?> 
               a bien été validée.</p>
<?php } ?>