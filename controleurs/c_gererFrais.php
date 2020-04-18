<?php
/**
 * Gestion des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
$estFicheValidee = false;
$idVisiteur = $_SESSION['idUtilisateur'];
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if ($typeUtilisateur == 'comptable') {
    $nomEtPrenomVisiteur = $pdo->getNomEtPrenomVisiteur(
        $_SESSION['idVisiteurSelectionne']
    );
} else {
    $nomEtPrenomVisiteur = $pdo->getNomEtPrenomVisiteur($idVisiteur);
}

if ($typeUtilisateur == 'comptable') {
    $nbJustificatifsDeBase = $pdo->getNbjustificatifs(
        $_SESSION['idVisiteurSelectionne'], 
        $_SESSION['moisSelectionne']
    );
}

$lesVisiteurs = $pdo->getLesVisiteurs();
$lesMois = $pdo->getTousLesMois();
require 'vues/v_listeVisiteur.php';
switch ($action) {
case 'saisirFrais':
    if ($pdo->estPremierFraisMois($idVisiteur, $mois)) {
        $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
    }
    break;
case 'validerMajFraisForfait':
    $lesFrais = filter_input(
        INPUT_POST, 'lesFrais', 
        FILTER_DEFAULT, 
        FILTER_FORCE_ARRAY
    );
    /** Si les quantités récupérées sont valides, alors on met
     * à jour le frais forfait. Dans le cas ou c'est un comptable
     * qui a corrigé les frais, il faut mettre à jour les frais
     * sur la fiche du visiteur selectionné.
     */
    if (lesQteFraisValides($lesFrais)) {
        if ($typeUtilisateur == 'comptable') {
            $pdo->majFraisForfait(
                $_SESSION['idVisiteurSelectionne'], 
                $_SESSION['moisSelectionne'], 
                $lesFrais
            );
        } else {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
        }
        $estMajFraisForfait = true;
    } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
    }
    break;
case 'validerCreationFrais':
    $dateFrais = filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_STRING);
    $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
    $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
    valideInfosFrais($dateFrais, $libelle, $montant);
    if (nbErreurs() != 0) {
        include 'vues/v_erreurs.php';
    } else {
        $pdo->creeNouveauFraisHorsForfait(
            $idVisiteur,
            $mois,
            $libelle,
            $dateFrais,
            $montant
        );
    }
    $estMajFraisHorsForfait = true;
    break;
case 'supprimerFrais':
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    $pdo->supprimerFraisHorsForfait($idFrais);
    $FraisHorsForfaitSupprime = true;
    break;
}
if ($typeUtilisateur == 'comptable') {
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait(
        $_SESSION['idVisiteurSelectionne'], 
        $_SESSION['moisSelectionne']
    );
    $lesFraisForfait = $pdo->getLesFraisForfait(
        $_SESSION['idVisiteurSelectionne'], 
        $_SESSION['moisSelectionne']
    );
} else {
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
}

require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
