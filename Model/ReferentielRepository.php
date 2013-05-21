<?php
include_once 'CommunModel.php';
include_once 'Constante.php';
include_once 'Referentiel.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class ReferentielRepository {

	const CODE_PRIX_NUIT_CAMPEUR_ADULTE = 'PRIX_NUIT_CAMPEUR_ADULTE';
	const CODE_PRIX_NUIT_CAMPEUR_ENFANT = 'PRIX_NUIT_CAMPEUR_ENFANT';
	const CODE_PRIX_NUIT_ANIMAL = 'PRIX_NUIT_ANIMAL';
	const CODE_PRIX_NUIT_PETITE_TENTE_VAN = 'PRIX_NUIT_PETITE_TENTE_VAN';
	const CODE_PRIX_NUIT_GRANDE_TENTE_CARAVANE = 'PRIX_NUIT_GRANDE_TENTE_CARAVANE';
	const CODE_PRIX_NUIT_CAMPING_CAR = 'PRIX_NUIT_CAMPING_CAR';
	const CODE_PRIX_NUIT_ELECTRICITE = 'PRIX_NUIT_ELECTRICITE';
	const CODE_PRIX_NUIT_VEHICULE_SUPP = 'PRIX_NUIT_VEHICULE_SUPP';
	const CODE_PRIX_NUIT_VISITEUR = 'PRIX_NUIT_VISITEUR';
	const CODE_PRIX_ROULOTTE_ROUGE_PERIODE_BASSE = 'PRIX_ROULOTTE_ROUGE_PERIODE_BASSE';
	const CODE_PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE = 'PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE';
	const CODE_PRIX_ROULOTTE_BLEUE_PERIODE_BASSE = 'PRIX_ROULOTTE_BLEUE_PERIODE_BASSE';
	const CODE_PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE = 'PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE';
	const CODE_DATE_DEBUT_PERIODE_HAUTE_ROULOTTE = 'DATE_DEBUT_PERIODE_HAUTE_ROULOTTE';
	const CODE_DATE_FIN_PERIODE_HAUTE_ROULOTTE = 'DATE_FIN_PERIODE_HAUTE_ROULOTTE';
	const CODE_DATE_DEBUT_AFFICHAGE_TABLEAU_RESERVATION = 'DATE_DEBUT_AFFICHAGE_TABLEAU_RESERVATION';
	const CODE_DATE_FIN_AFFICHAGE_TABLEAU_RESERVATION = 'DATE_FIN_AFFICHAGE_TABLEAU_RESERVATION';

	/**
	 * Connexion à la base de données
	 * @var unknown_type
	 */
	private $mysqli;

	/**
	 * Tableau contenant tout le référentiel
	 * @var array
	 */
	private $referentiel = null;

	public function __construct() {
		//Construction des singleton
	}

	/**
	 * Renvoie le prix d'un campeur adulte pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un campeur adulte pour une nuit
	 */
	public function getPrixCampeurAdulte() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_CAMPEUR_ADULTE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un campeur adulte pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un campeur adulte pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixCampeurAdulte($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_CAMPEUR_ADULTE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'un campeur enfant pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un campeur enfant pour une nuit
	 */
	public function getPrixCampeurEnfant() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_CAMPEUR_ENFANT);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un campeur enfant pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un campeur enfant pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixCampeurEnfant($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_CAMPEUR_ENFANT, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'un animal pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un animal pour une nuit
	 */
	public function getPrixAnimal() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_ANIMAL);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un animal pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un animal pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixAnimal($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_ANIMAL, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'une petite tente ou van pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'une petite tente ou van pour une nuit
	 */
	public function getPrixPetiteTenteVan() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_PETITE_TENTE_VAN);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'une petite tente ou van pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'une petite tente ou van pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixPetiteTenteVan($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_PETITE_TENTE_VAN, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'une grande tente ou caravane pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'une grande tente ou caravane pour une nuit
	 */
	public function getPrixGrandeTenteCaravane() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_GRANDE_TENTE_CARAVANE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'une grande tente ou caravane pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'une grande tente ou caravane pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixGrandeTenteCaravane($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_GRANDE_TENTE_CARAVANE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'un camping car pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un camping car pour une nuit
	 */
	public function getPrixCampingCar() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_CAMPING_CAR);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un camping car pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un camping car pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixCampingCar($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_CAMPING_CAR, $valeur, $flush);
	}

	/**
	 * Renvoie le prix de l'électricité pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix de l'électricité pour une nuit
	 */
	public function getPrixElectricite() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_ELECTRICITE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix de l'électricité pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix de l'électricité pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixElectricite($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_ELECTRICITE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'un véhicule supplémentaire pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un véhicule supplémentaire pour une nuit
	 */
	public function getPrixVehiculeSupp() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_VEHICULE_SUPP);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un véhicule supplémentaire pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un véhicule supplémentaire pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixVehiculeSupp($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_VEHICULE_SUPP, $valeur, $flush);
	}

	/**
	 * Renvoie le prix d'un visiteur pour une nuit
	 * @author Arnaud DUPUIS
	 * @return float Prix d'un visiteur pour une nuit
	 */
	public function getPrixVisiteur() {
		$retour = $this->getRef(self::CODE_PRIX_NUIT_VISITEUR);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix d'un visiteur pour une nuit
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix d'un visiteur pour une nuit
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixVisiteur($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_NUIT_VISITEUR, $valeur, $flush);
	}

	/**
	 * Renvoie le prix de la roulotte rouge en période basse
	 * @author Arnaud DUPUIS
	 * @return float Prix de la roulotte rouge en période basse
	 */
	public function getPrixRoulotteRougePeriodeBasse() {
		$retour = $this->getRef(self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_BASSE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix de la roulotte rouge en période basse
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix de la roulotte rouge en période basse
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixRoulotteRougePeriodeBasse($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_BASSE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix de la roulotte rouge en période haute
	 * @author Arnaud DUPUIS
	 * @return float Prix de la roulotte rouge en période haute
	 */
	public function getPrixRoulotteRougePeriodeHaute() {
		$retour = $this->getRef(self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix de la roulotte rouge en période haute
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix de la roulotte rouge en période haute
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixRoulotteRougePeriodeHaute($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix de la roulotte bleue en période basse
	 * @author Arnaud DUPUIS
	 * @return float Prix de la roulotte bleue en période basse
	 */
	public function getPrixRoulotteBleuePeriodeBasse() {
		$retour = $this->getRef(self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_BASSE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix de la roulotte bleue en période basse
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix de la roulotte bleue en période basse
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixRoulotteBleuePeriodeBasse($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_BASSE, $valeur, $flush);
	}

	/**
	 * Renvoie le prix de la roulotte bleue en période haute
	 * @author Arnaud DUPUIS
	 * @return float Prix de la roulotte bleue en période haute
	 */
	public function getPrixRoulotteBleuePeriodeHaute() {
		$retour = $this->getRef(self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = floatval($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise le prix de la roulotte bleue en période haute
	 * @author Arnaud DUPUIS
	 * @param float $valeur Prix de la roulotte bleue en période haute
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setPrixRoulotteBleuePeriodeHaute($valeur, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = floatval($valeur);
		}

		$this->setRef(self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE, $valeur, $flush);
	}

	/**
	 * Renvoie la date de début de la période haute pour les roulottes
	 * @author Arnaud DUPUIS
	 * @return DateTime Date de début de la période haute pour les roulottes
	 */
	public function getDateDebutPeriodeHauteRoulotte() {
		$retour = $this->getRef(self::CODE_DATE_DEBUT_PERIODE_HAUTE_ROULOTTE);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = new DateTime($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise la date de début de la période haute pour les roulottes
	 * @author Arnaud DUPUIS
	 * @param \DateTime $valeur Date de début de la période haute pour les roulottes
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setDateDebutPeriodeHauteRoulotte(\DateTime $valeur = null, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = $valeur->format('Y-m-d');
		}

		$this->setRef(self::CODE_DATE_DEBUT_PERIODE_HAUTE_ROULOTTE, $valeur, $flush);
	}
	
	/**
	 * Renvoie la date de fin de la période haute pour les roulottes
	 * @author Arnaud DUPUIS
	 * @return DateTime Date de fin de la période haute pour les roulottes
	 */
	public function getDateFinPeriodeHauteRoulotte() {
		$retour = $this->getRef(self::CODE_DATE_FIN_PERIODE_HAUTE_ROULOTTE);
	
		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = new DateTime($retour->getValeur());
		}
	
		return $retour;
	}
	
	/**
	 * Initialise la date de fin de la période haute pour les roulottes
	 * @author Arnaud DUPUIS
	 * @param \DateTime $valeur Date de fin de la période haute pour les roulottes
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setDateFinPeriodeHauteRoulotte(\DateTime $valeur = null, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = $valeur->format('Y-m-d');
		}
	
		$this->setRef(self::CODE_DATE_FIN_PERIODE_HAUTE_ROULOTTE, $valeur, $flush);
	}

	/**
	 * Renvoie la date de début de l'affichage du tableau de réservations
	 * @author Arnaud DUPUIS
	 * @return DateTime Date de début de l'affichage du tableau de réservations
	 */
	public function getDebutAffichageTableauReservations() {
		$retour = $this->getRef(self::CODE_DATE_DEBUT_AFFICHAGE_TABLEAU_RESERVATION);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = new DateTime($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise la date de début de l'affichage du tableau de réservations
	 * @author Arnaud DUPUIS
	 * @param \DateTime $valeur Date de début de l'affichage du tableau de réservations
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setDebutAffichageTableauReservations(\DateTime $valeur = null, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = $valeur->format('Y-m-d');
		}

		$this->setRef(self::CODE_DATE_DEBUT_AFFICHAGE_TABLEAU_RESERVATION, $valeur, $flush);
	}

	/**
	 * Renvoie la date de fin de l'affichage du tableau de réservations
	 * @author Arnaud DUPUIS
	 * @return DateTime Date de fin de l'affichage du tableau de réservations
	 */
	public function getFinAffichageTableauReservations() {
		$retour = $this->getRef(self::CODE_DATE_FIN_AFFICHAGE_TABLEAU_RESERVATION);

		//Mise en forme
		if (!is_null($retour) && !is_null($retour->getValeur())) {
			$retour = new DateTime($retour->getValeur());
		}

		return $retour;
	}

	/**
	 * Initialise la date de fin de l'affichage du tableau de réservations
	 * @author Arnaud DUPUIS
	 * @param \DateTime $valeur Date de fin de l'affichage du tableau de réservations
	 * @param Boolean $flush Enregistrer ou pas en base de données
	 */
	public function setFinAffichageTableauReservations(\DateTime $valeur = null, $flush = false) {
		//Mise en forme
		if (!is_null($valeur)) {
			$valeur = $valeur->format('Y-m-d');
		}

		$this->setRef(self::CODE_DATE_FIN_AFFICHAGE_TABLEAU_RESERVATION, $valeur, $flush);
	}

	/**
	 * Récupère le référentiel demandé en fonction de son code liste
	 * Si le référentiel n'existe pas, null est renvoyé
	 * @param string $codeReferentiel
	 * @return string Valeur du référentiel demandé. Renvoie null sinon
	 */
	private function getRef($codeReferentiel) {
		$retour = null;

		//On charge le référentiel si c'est la première fois
		if ($this->referentiel == null) {
			$this->chargerReferentiel();
		}

		if (array_key_exists($codeReferentiel, $this->referentiel)) {
			$retour = $this->referentiel[$codeReferentiel];
		}

		return $retour;
	}

	/**
	 * Enregistre le référentiel demandé en fonction de son code liste
	 * @param string $codeReferentiel Code du référentiel à enregistrer
	 * @param mixed $valeur Valeur à enregistrer
	 * @param boolean $flush Si true, on enregistre tout le référentiel
	 * dans la base de données
	 * @return boolean Renvoie true si tout est OK
	 */
	private function setRef($codeReferentiel, $valeur, $flush) {
		$retour = true;

		//On charge le référentiel si c'est la première fois
		if ($this->referentiel == null) {
			$this->chargerReferentiel();
		}

		if (!is_null($valeur)) {
			if (is_null($this->referentiel[$codeReferentiel])) {
				//Si le réferentiel n'existe pas encore, on le créé
				$this->referentiel[$codeReferentiel] = new Referentiel();
				$this->referentiel[$codeReferentiel]->setCode($codeReferentiel);
			}
			$this->referentiel[$codeReferentiel]->setValeur($valeur);
			$this->referentiel[$codeReferentiel]->setAnneeValidite(date('Y'));
		}

		//Enregistrement dans la base de données si demandé
		if ($flush === true) {
			$this->enregistrerReferentiel();
		}

		return $retour;
	}

	/**
	 * Charge le référentiel depuis la base de données. Alimente l'attribut $referentiel
	 */
	private function chargerReferentiel() {
		$this->referentiel = array();

		//Initialisation du tableau de référentiels
		$this->referentiel[self::CODE_PRIX_NUIT_CAMPEUR_ADULTE] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_CAMPEUR_ENFANT] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_ANIMAL] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_PETITE_TENTE_VAN] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_GRANDE_TENTE_CARAVANE] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_CAMPING_CAR] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_ELECTRICITE] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_VEHICULE_SUPP] = null;
		$this->referentiel[self::CODE_PRIX_NUIT_VISITEUR] = null;
		$this->referentiel[self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_BASSE] = null;
		$this->referentiel[self::CODE_PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE] = null;
		$this->referentiel[self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_BASSE] = null;
		$this->referentiel[self::CODE_PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE] = null;
		$this->referentiel[self::CODE_DATE_DEBUT_PERIODE_HAUTE_ROULOTTE] = null;
		$this->referentiel[self::CODE_DATE_DEBUT_AFFICHAGE_TABLEAU_RESERVATION] = null;
		$this->referentiel[self::CODE_DATE_FIN_AFFICHAGE_TABLEAU_RESERVATION] = null;

		//Requête SQL pour rechercher les clients
		$sql = 'SELECT ref.id, ref.code, ref.valeur, ref.annee_validite, '
		. 'ref.date_creation, ref.date_modification '
		. 'FROM referentiel ref';

		$this->initBdd();

		//On envoie la requête
		$result = $this->executerSQL($sql);

		//On récupère les résultats
		while($data = $result->fetch_row()) {

			//Création du référentiel
			$referentiel = new Referentiel();
			$referentiel->setId($data[0]);
			$referentiel->setCode($data[1]);
			$referentiel->setValeur($data[2]);
			$referentiel->setAnneeValidite($data[3]);
			$referentiel->setDateCreation(new DateTime($data[4]));
			$referentiel->setDateModification(new DateTime($data[5]));

			//En fonction du code on l'enregistre dans le tableau des référentiels
			if (!is_null($referentiel->getCode())) {
				$this->referentiel[$referentiel->getCode()] = $referentiel;
			}
		}

		$this->fermerBdd();
	}

	/**
	 * Enregistrement en BDD des référentiels
	 */
	private function enregistrerReferentiel() {
		$sql = '';

		$this->initBdd();

		if (!is_null($this->referentiel)) {
			foreach ($this->referentiel as $key => $objRef) {
				//Si l'id de l'objet n'existe pas, on créé une ligne en BDD sinon on la modifie
				if (!is_null($objRef) && !is_null($objRef->getValeur())) {
					if (is_null($objRef->getId())) {
						//Requête de création du référentiel
						$sql .= "\n INSERT INTO referentiel (code, valeur, annee_validite) "
						. "VALUES ("
						. "'" . $objRef->getCode() . "', "
						. "'" . $this->mysqli->real_escape_string($objRef->getValeur()) . "', "
						. "'" . $objRef->getAnneeValidite() . "'"
						. ");";
					}
					else {
						//Requête de modification du référentiel
						$sql .= "\n UPDATE referentiel ref SET "
						. "ref.code = '" . $objRef->getCode() . "', "
						. "ref.valeur = '" . $this->mysqli->real_escape_string($objRef->getValeur())  . "', "
						. "ref.annee_validite = '" . $objRef->getAnneeValidite() . "'"
						. " WHERE ref.id = " . $objRef->getId(). ";";
					}
				}
			}
		}

		//On envoie la requête
		$result = $this->executerSQL($sql, true);

		$this->fermerBdd();

		//Le tableau des référentiels devra être réinitialisé au prochain coup
		$this->referentiel = null;
	}

	/**
	 * Fonction initialisant la connexion à la base de données
	 */
	private function initBdd() {
		//On se connecte à MySQL
		$this->mysqli = new mysqli(Constante::SERVEUR_BDD, Constante::LOGIN_BDD, Constante::PASSWORD_BDD);

		//On sélectionne la base de camping
		$this->mysqli->select_db(Constante::NOM_BASE_CAMPING);
	}

	/**
	 * Exécute la commande SQL passée en paramètre
	 * @param string $sql
	 * @return Renvoie le résultat de la requête
	 */
	private function executerSQL($sql, $requeteMultiple = false) {

		if ($requeteMultiple === true) {
			$retour = $this->mysqli->multi_query($sql) or die("Erreur SQL !\n" . $sql . "\n" . $this->mysqli->error);
		}
		else {
			$retour = $this->mysqli->query($sql) or die("Erreur SQL !\n" . $sql . "\n" . $this->mysqli->error);
		}

		return $retour;
	}

	/**
	 * Fonction fermant la connexion à la base de données
	 */
	private function fermerBdd() {
		//On ferme la connexion à MySQL
		$this->mysqli->close();
	}
}

?>