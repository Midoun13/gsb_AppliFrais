<?php

namespace PdoGsb;
use PDO;

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $_monPdo de type PDO
 * $_monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $_serveur = 'mysql:host=localhost';
    private static $_bdd = 'dbname=gsb_frais';
    private static $_user = 'userGsb';
    private static $_mdp = 'secret';
    private static $_monPdo;
    private static $_monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$_monPdo = new PDO(
            PdoGsb::$_serveur . ';' . PdoGsb::$_bdd,
            PdoGsb::$_user,
            PdoGsb::$_mdp
        );
        PdoGsb::$_monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$_monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$_monPdoGsb == null) {
            PdoGsb::$_monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$_monPdoGsb;
    }

    /**
     * Fonction statique qui retourne l'objet PDO de connexion
     * à la BDD. Si l'objet est vide, on créé une instance de la 
     * classe par l'appel de la méthode getPdoGsb().
     *
     * @return l'unique objet PDO de la classe PdoGsb
     */
    public static function getMonPdo($typePdo) 
    {
        if (PdoGsb::$_monPdo == null) {
            PdoGsb::getPdoGsb($typePdo);
        }
        return PdoGsb::$_monPdo;
    }

    /**
     * Retourne les informations d'un comptable sous condition que 
     * le mdp saisi par le visiteur soit correct
     * 
     * @param String $login Login du comptable
     * @param String $mdp   Mot de passe du comptable
     * 
     * @return l'id, le nom et le prénom du comptable sous la forme d'un 
     * tableau associatif
     */
    public function getInfosComptable($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
            . 'comptable.prenom AS prenom '
            . 'FROM comptable '
            . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /** 
     * Retourne le nom et prénom du visiteur
     * 
     * @param String $id Id du visiteur
     * 
     * @return le nom et prénom du visiteur sous forme d'un tableau associatif 
     * comprenant comme clé nom et prenom contenant respectivement le nom et 
     * prénom du visiteur
     */
    public function getNomEtPrenomVisiteur($id)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.nom AS nom, visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.id = :unId'
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne les informations de tous les visiteurs
     * 
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll(PDO::FETCH_ASSOC);;
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        if (isset($lesFrais)) {
            $lesCles = array_keys($lesFrais);
        }
        if (isset($lesCles)) {
            foreach ($lesCles as $unIdFrais) {
                $qte = $lesFrais[$unIdFrais];
                $requetePrepare = PdoGSB::$_monPdo->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
                );
                $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
                $requetePrepare->bindParam(
                    ':unIdVisiteur', 
                    $idVisiteur, 
                    PDO::PARAM_STR
                );
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$_monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquels un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les mois depuis septembre 2016 jusqu'à la date actuelle
     * 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant jusqu'au mois actuel
     */
    public function getTousLesMois() 
    {
        $dateActuelle = date('Ym');
        $annee = substr($dateActuelle, 0, 4);
        $numMoisActuelle = substr($dateActuelle, 4, 2);
        $lesNumMois = array('01', '02', '03', '04', '05', 
        '06', '07', '08', '09', '10', '11', '12');
        $lesMois = array();
        for ($a = $annee; $a >= 2016; $a--) {
            if ($a == $annee) {
                for ($i = 0; $i < count($lesNumMois)-1; $i++) {
                    if ($numMoisActuelle == $lesNumMois[$i]) {
                        $indexTableau = $i;
                    }
                };
                for ($m = $indexTableau; $m >= $indexTableau; $m--) {
                    $lesMois[] = array(
                        'mois' => $a . $lesNumMois[$m],
                        'numAnnee' => $a,
                        'numMois' => $lesNumMois[$m]
                    );
                }
            } else {
                for ($m = count($lesNumMois)-1; $m >= 0; $m--) {
                    $lesMois[] = array(
                        'mois' => $a . $lesNumMois[$m],
                        'numAnnee' => $a,
                        'numMois' => $lesNumMois[$m]
                    );
                    if ($a == '2016' && $m == 8) {
                        $a = 2015;
                        $m = -1;
                    }
                }
            }
            
        }
        return $lesMois;
    }

    /**
     * Retourne les mois des fiches étant dans l'état clôturé pour un visiteur donné
     * 
     * @param String $idVisiteur ID du visiteur
     * 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisFicheClotureVisiteur($idVisiteur) 
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.mois as mois '
            . ' FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . "AND fichefrais.idetat = 'CL'"
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMoisRetournes = $requetePrepare->fetchAll();
        $lesMois = array();
        foreach ($lesMoisRetournes as $unMois) {
            $lesMois[] = array(
                'mois' => strval($unMois['mois']),
                'numAnnee' => substr(strval($unMois['mois']), 0, 4),
                'numMois' => substr(strval($unMois['mois']), 4, 2)
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch(PDO::FETCH_ASSOC);
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /** 
     * Met à jour un frais hors forfait pour un visiteur et un mois donné
     * à partir des informations fournies en paramètre 
     * 
     * @param String $idFraisHorsForfait ID du frais hors forfait
     * @param String $idVisiteur         ID du visiteur
     * @param String $mois               Mois sous la forme aaaamm
     * @param String $libelle            Libellé du frais
     * @param String $date               Date du frais au format français jj//mm/aaaa
     * @param Float  $montant            Montant du frais
     * 
     * @return null
     */
    public function majFraisHorsForfait(
        $idFraisHorsForfait,
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET libelle = :unLibelle, date = :uneDate, '
            . 'montant = :unMontant '
            . 'WHERE id = :unId AND idVisiteur = :unIdVisiteur AND '
            . 'mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDate', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $idFraisHorsForfait, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Permet de valider la fiche pour un visiteur donné et un mois donné
     * en modifiant la date de modification de la fiche à celle du jour actuel et
     * en affectant le comptable qui a effectué la validation à la fiche
     * 
     * @param String $idVisiteur  ID du visiteur
     * @param String $idComptable ID du comptable
     * @param String $mois        Mois sous la forme aaaamm
     * 
     * @return null
     */
    public function validerLaFiche($idVisiteur, $idComptable, $mois) 
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idcomptable = :unIdComptable, '
            . "idetat = 'VA', "
            . 'datemodif = now() '
            . 'WHERE idvisiteur = :unIdVisiteur AND '
            . 'mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdComptable', $idComptable, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne le montant validé d'une fiche pour un visiteur donné 
     * et un mois donné sans prendre en compte les frais hors forfait refusés
     * 
     * @param String $idVisiteur id du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * 
     * @return le montant validé
     */
    public function getMontantValideHorsFraisRefuses($idVisiteur, $mois) 
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            "SELECT SUM(quantite * montant) AS 'montant valide' "
            . 'FROM fraisforfait JOIN lignefraisforfait '
            . 'ON idfraisforfait = id '
            . 'WHERE idvisiteur = :unIdVisiteur AND '
            . 'mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        
        $montant = $requetePrepare->fetch();
        $montantFraisForfait = $montant['montant valide'];

        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT montant, libelle '
            . 'FROM lignefraishorsforfait '
            . 'WHERE idvisiteur = :unIdVisiteur AND '
            . 'mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $fraisHorsForfait = $requetePrepare->fetchAll();
        $montantFraisHorsForfait = 0;
        foreach ($fraisHorsForfait as $unFraisHorsForfait) {
            // Il ne faut pas prendre en compte les frais hors forfait refusés
            if (substr($unFraisHorsForfait['libelle'], 0, 6) != 'REFUSE') {
                $montantFraisHorsForfait+= $unFraisHorsForfait['montant'];
            }
        }
        $ficheMontantValide = $montantFraisForfait + $montantFraisHorsForfait;
        return $ficheMontantValide;
    }

    /**
     * Met à jour le montant validé d'une fiche pour un visiteur donné
     * et un mois donné
     * 
     * @param String $idVisiteur    id du visiteur
     * @param String $mois          mois sous la forme aaaamm
     * @param String $montantValide montant validé pour la fiche
     * 
     * @return null
     */
    public function majMontantValide($idVisiteur, $mois, $montantValide)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET montantvalide = :unMontantValide '
            . 'WHERE idvisiteur = :unIdVisiteur AND '
            . 'mois = :unMois'
        );
        $requetePrepare->bindParam(
            'unMontantValide', 
            $montantValide, 
            PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
}
