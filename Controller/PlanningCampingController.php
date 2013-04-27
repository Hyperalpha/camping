<?php
include_once '../Model/ExportExcelRepository.php';
include_once '../Model/ReferentielRepository.php';
include_once '../Model/ReservationRepository.php';
include_once '../Model/Reservation.php';
include_once '../Model/Client.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class PlanningCampingController {

	//Repository
	private $reservationRepository;
	private $exportExcelRepository;
	private $referentielRepository;
	
	const DEFAULT_DATE_DEBUT = "15 June";
	const DEFAULT_DATE_FIN = "15 September";

	//Une erreur se d�clanche si plus de x jours sont pr�sents dans une ann�e
	const WATCHDOG_JOURS_ANNEE = 370;

	public function __construct() {
		//Construction des singleton
		$this->reservationRepository = new ReservationRepository();
		$this->exportExcelRepository = new ExportExcelRepository();
		$this->referentielRepository = new ReferentielRepository();
	}

	/**
	 * Renvoie un tableau contenant toutes les r�servations de l'ann�e en cours
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau de r�servation
	 */
	public function recupererToutesReservations() {
		$retour = array();

		//On r�cup�re toute les r�servations
		$annee = date("Y");
		$retour = $this->reservationRepository->rechercherToutesReservations($annee);

		return $retour;
	}

	/**
	 * Renvoie un tableau contenant toutes les r�servations de la journ�e en cours
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau de r�servation
	 */
	public function recupererReservationsDuJour() {
		$retour = array();

		//On r�cup�re toute les r�servations
		$annee = date("Y");
		$mois = date("m");
		$jour = date("d");
		$retour = $this->reservationRepository->rechercherToutesReservations($annee, $mois, $jour);
			
		return $retour;
	}

	/**
	 * G�n�ration du calendrier de l'ann�e
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau contenant les jours et les mois de l'ann�e
	 */
	public function construireCalendrierReservations() {
		//Timestamp de d�but et de fin du tableau
		/**
		 * @TODO: reste un bug : si on change l'interval d'affichage, il n'est pas pris en compte directement. Il faut recharger la page//
		 */
		$dateDebut = $this->referentielRepository->getDebutAffichageTableauReservations();
		$dateFin = $this->referentielRepository->getFinAffichageTableauReservations();
		if (is_null($dateDebut)) {
			//Dates par d�faut
			$dateDebut = new DateTime(self::DEFAULT_DATE_DEBUT);
		}
		if (is_null($dateFin)) {
			//Dates par d�faut
			$dateFin = new DateTime(self::DEFAULT_DATE_FIN);
		}
		$timestpEnCours = $dateDebut->getTimestamp();
		$timestpFin = $dateFin->getTimestamp();
		$tabCalendrier = null;
		$i = 0;
		//Traduction des mois
		$tabTransMois = array("",
		Translation::JANVIER,
		Translation::FEVRIER,
		Translation::MARS,
		Translation::AVRIL,
		Translation::MAI,
		Translation::JUIN,
		Translation::JUILLET,
		Translation::AOUT,
		Translation::SEPTEMBRE,
		Translation::OCTOBRE,
		Translation::NOVEMBRE,
		Translation::DECEMBRE,
		);

		while (($timestpEnCours <= $timestpFin) && ($i < self::WATCHDOG_JOURS_ANNEE)) {
			//Construction du tableau avec les jours
			$annee = date("Y", $timestpEnCours);
			$strMois = $tabTransMois[date("n", $timestpEnCours)];
			$jour = date("j", $timestpEnCours);
			$dateComplete = date("d", $timestpEnCours) . "/" . date("m", $timestpEnCours)
			. "/" . $annee;

			$tabCalendrier[$annee][$strMois][$jour] = $dateComplete;

			//Incr�ment 1 jour
			$timestpEnCours = strtotime('+1 day', $timestpEnCours);

			//Watchdog
			$i++;
		}

		return $tabCalendrier;
	}

	/**
	 * Enregistre une r�servation � partir de donn�es s�rialis�es en entr�e
	 * @author Arnaud DUPUIS
	 * @param string $strReservation
	 * @return integer Renvoie l'identifiant de la r�servation. False sinon
	 */
	public function enregistrerReservation($strReservation) {
		$retour = false;

		try {
			$reservation = $this->parseInfosReservation($strReservation);
			if ($reservation) {
				$newReservation = $this->reservationRepository->enregistrerReservation($reservation);
			}
			$retour = $newReservation->getReference() . "|"
			. $newReservation->getClient()->getReference();
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Enregistre le num�ro de l'emplacement d'une r�servation
	 * @author Arnaud DUPUIS
	 * @param string $refFiche
	 * @param integer $numeroEmplacement
	 * @return Boolean Renvoie true si succes, false sinon
	 */
	public function enregistrerNumeroEmplacement($refFiche, $numeroEmplacement) {
		$retour = false;

		try {
			if (!is_null($refFiche)) {
				$reservation = $this->reservationRepository->rechercherReservationParReference($refFiche);

				if (!is_null($reservation[0])) {
					$reservation[0]->setNumeroEmplacement($numeroEmplacement);

					//On enregistre les nouvelles coordonn�es
					$newReservation = $this->reservationRepository->enregistrerReservation($reservation[0]);

					$retour = true;
				}
			}
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Enregistre les coordonn�es de l'emplacement d'une r�servation
	 * @author Arnaud DUPUIS
	 * @param string $refFiche
	 * @param integer $coordonneesX
	 * @param integer $coordonneesY
	 * @return Boolean Renvoie true si succes, false sinon
	 */
	public function enregistrerEmplacement($refFiche, $coordonneesX, $coordonneesY) {
		$retour = false;

		try {
			if (!is_null($refFiche)) {
				$reservation = $this->reservationRepository->rechercherReservationParReference($refFiche);

				if (!is_null($reservation[0])) {
					$reservation[0]->setCoordonneesXEmplacement($coordonneesX);
					$reservation[0]->setCoordonneesYEmplacement($coordonneesY);

					//On enregistre les nouvelles coordonn�es
					$newReservation = $this->reservationRepository->enregistrerReservation($reservation[0]);

					$retour = true;
				}
			}
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Supprime une r�servation � partir de l'id pass� en entr�e
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la r�servation � supprimer
	 * @return Boolean Renvoie true si r�ussit, false sinon
	 */
	public function supprimerReservation($idReservation) {
		$retour = false;

		try {
			$retour = $this->reservationRepository->supprimerReservation($idReservation);
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * S�rialise un objet Reservation pour pouvoir le passer � l'IHM
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation
	 * @return string Renvoie les infos s�rialis�es
	 */
	public function convertirReservationPourIHM(Reservation $reservation) {
		$chaineRetour = "";
		$sep = "|";
		$client = $reservation->getClient();

		//Version 1.0
		$chaineRetour .= "v1.0" . $sep;
		if (!is_null($client)) {
			//R�f�rence du client
			$chaineRetour .= $client->getReference() . $sep;
		}
		else {
			$chaineRetour .= $sep;
		}
		//R�f�rence r�servation
		$chaineRetour .= $reservation->getReference() . $sep;
		if (!is_null($client)) {
			//Nom
			$chaineRetour .= $client->getNom() . $sep;
			//Pr�nom
			$chaineRetour .= $client->getPrenom() . $sep;
			//Rue
			$chaineRetour .= $client->getAdresse1() . $sep;
			//Compl�ment adresse
			$chaineRetour .= $client->getAdresse2() . $sep;
			//Code postal
			$chaineRetour .= $client->getCodePostal() . $sep;
			//Ville
			$chaineRetour .= $client->getVille() . $sep;
			//Pays
			$chaineRetour .= $client->getPays() . $sep;
			//Portable
			$chaineRetour .= $client->getTelephonePortable() . $sep;
			//Email
			$chaineRetour .= $client->getEmail() . $sep;
		}
		else {
			$chaineRetour .= $sep . $sep . $sep . $sep . $sep . $sep . $sep . $sep . $sep . $sep;
		}
		//Piece d'identit�
		$piPresentee = $reservation->getPieceIdPresentee();

		switch ($piPresentee) {
			case Reservation::carteId:
				$strPi = "carteId";
				break;
			case Reservation::autreId:
				$strPi = "autre";
				break;
			default:
				$strPi = "autre";
				break;
		}
		$chaineRetour .= $strPi . $sep;
		//Date d'arriv�e
		$chaineRetour .= $reservation->getDateArrivee()->format('d/m/Y') . $sep;
		//Date de d�part
		$chaineRetour .= $reservation->getDateDepart()->format('d/m/Y') . $sep;
		//Nombre d'adultes
		$chaineRetour .= intval($reservation->getNombreAdultes()) . $sep;
		//Nombre d'enfants
		$chaineRetour .= intval($reservation->getNombreEnfants()) . $sep;
		//Nombre d'animaux
		$chaineRetour .= intval($reservation->getNombreAnimaux()) . $sep;
		//Nombre de petites tentes
		$chaineRetour .= intval($reservation->getNombrePetitesTentes()) . $sep;
		//Nombre de grandes tentes
		$chaineRetour .= intval($reservation->getNombreGrandesTentes()) . $sep;
		//Nombre de caravanes
		$chaineRetour .= intval($reservation->getNombreCaravanes()) . $sep;
		//Nombre de vans
		$chaineRetour .= intval($reservation->getNombreVans()) . $sep;
		//Nombre de camping cars
		$chaineRetour .= intval($reservation->getNombreCampingCars()) . $sep;
		//Electricit�
		$chaineRetour .= $reservation->getElectricite() . $sep;
		//Nombre de nuit�s visiteur
		$chaineRetour .= intval($reservation->getNombreNuitesVisiteur()) . $sep;
		//Observations
		$chaineRetour .= $reservation->getObservations() . $sep;
		//Id du bloc de r�servation (non pris en charge par le PHP)
		$chaineRetour .= $sep;
		//Arrhes sur la r�servation
		$chaineRetour .= $reservation->getArrhes() . $sep;
		// Num�ro d'emplacement de la r�servation
		$chaineRetour .= $reservation->getNumeroEmplacement() . $sep;

		return $chaineRetour;
	}

	/**
	 * Exporte la r�servation pr�cis�e vers un fichier Word ou Excel.
	 * Redirige la page pour afficher le document
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la r�servation � exporter
	 */
	public function exporterReservation($idReservation) {

		//On r�cup�re la r�servation
		$reservation = $this->reservationRepository->rechercherReservationParReference($idReservation);

		if (count($reservation) == 1) {
			$urlDocument = $this->exportExcelRepository->exporterReservation($reservation[0]);

			if ($urlDocument) {
				header('Location: ../Model/' . $urlDocument);
			}
		}
	}

	/**
	 * Charge la popup de r�glages avec les valeurs du r�f�rentiel
	 * @author Arnaud DUPUIS
	 * @return string Renvoie la popup charg�e
	 */
	public function chargerPopupReglages() {
		$retour = null;

		try {
			$retour = file_get_contents('../IHM/popup_reglages.html');

			//On remplit la popup avec les valeurs du r�f�rentiel
			$retour = str_replace('{{PRIX_NUIT_ADULTE}}', $this->referentielRepository->getPrixCampeurAdulte(), $retour);
			$retour = str_replace('{{PRIX_NUIT_ENFANT}}', $this->referentielRepository->getPrixCampeurEnfant(), $retour);
			$retour = str_replace('{{PRIX_NUIT_ANIMAL}}', $this->referentielRepository->getPrixAnimal(), $retour);
			$retour = str_replace('{{PRIX_NUIT_PETIT_EMPLACEMENT}}', $this->referentielRepository->getPrixPetiteTenteVan(), $retour);
			$retour = str_replace('{{PRIX_NUIT_GRAND_EMPLACEMENT}}', $this->referentielRepository->getPrixGrandeTenteCaravane(), $retour);
			$retour = str_replace('{{PRIX_NUIT_EMPLACEMENT_CAMPING_CAR}}', $this->referentielRepository->getPrixCampingCar(), $retour);
			$retour = str_replace('{{PRIX_NUIT_ELECTRICITE}}', $this->referentielRepository->getPrixElectricite(), $retour);
			$retour = str_replace('{{PRIX_NUIT_VEHICULE_SUPP}}', $this->referentielRepository->getPrixVehiculeSupp(), $retour);
			$retour = str_replace('{{PRIX_NUIT_VISITEUR}}', $this->referentielRepository->getPrixVisiteur(), $retour);

			$retour = str_replace('{{PRIX_ROULOTTE_ROUGE_PERIODE_BASSE}}', $this->referentielRepository->getPrixRoulotteRougePeriodeBasse(), $retour);
			$retour = str_replace('{{PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE}}', $this->referentielRepository->getPrixRoulotteRougePeriodeHaute(), $retour);
			$retour = str_replace('{{PRIX_ROULOTTE_BLEUE_PERIODE_BASSE}}', $this->referentielRepository->getPrixRoulotteBleuePeriodeBasse(), $retour);
			$retour = str_replace('{{PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE}}', $this->referentielRepository->getPrixRoulotteBleuePeriodeHaute(), $retour);

			$retour = str_replace('{{DATE_DEBUT_PERIODE_HAUTE_ROULOTTE}}', $this->formatterDateSansAnnee($this->referentielRepository->getDateDebutPeriodeHauteRoulotte()), $retour);
			$retour = str_replace('{{DATE_DEBUT_TABLEAU_RESERVATIONS}}', $this->formatterDateSansAnnee($this->referentielRepository->getDebutAffichageTableauReservations()), $retour);
			$retour = str_replace('{{DATE_FIN_TABLEAU_RESERVATIONS}}', $this->formatterDateSansAnnee($this->referentielRepository->getFinAffichageTableauReservations()), $retour);
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Enregistre la popup de r�glages avec les valeurs du r�f�rentiel
	 * @author Arnaud DUPUIS
	 * @param \stdClass $stdReglages R�glages � enregistrer sous forme de stdClass
	 * @return string Redirige vers la page de consultation des r�servations
	 */
	public function enregistrerReglages($stdReglages) {
		session_start();

		//On set les valeurs sans mettre � jour la base de donn�es
		try {
			$this->referentielRepository->setPrixCampeurAdulte($stdReglages->prixAdulte, false);
			$this->referentielRepository->setPrixCampeurEnfant($stdReglages->prixEnfant, false);
			$this->referentielRepository->setPrixAnimal($stdReglages->prixAnimal, false);
			$this->referentielRepository->setPrixPetiteTenteVan($stdReglages->prixPetitEmplacement, false);
			$this->referentielRepository->setPrixGrandeTenteCaravane($stdReglages->prixGrandEmplacement, false);
			$this->referentielRepository->setPrixCampingCar($stdReglages->prixCampingCar, false);
			$this->referentielRepository->setPrixElectricite($stdReglages->prixElectricite, false);
			$this->referentielRepository->setPrixVehiculeSupp($stdReglages->prixVehiculeSupp, false);
			$this->referentielRepository->setPrixVisiteur($stdReglages->prixVisiteur, false);

			$this->referentielRepository->setPrixRoulotteRougePeriodeBasse($stdReglages->prixRoulottesRougePeriodeBasse, false);
			$this->referentielRepository->setPrixRoulotteRougePeriodeHaute($stdReglages->prixRoulottesRougePeriodeHaute, false);
			$this->referentielRepository->setPrixRoulotteBleuePeriodeBasse($stdReglages->prixRoulottesBleuePeriodeBasse, false);
			$this->referentielRepository->setPrixRoulotteBleuePeriodeHaute($stdReglages->prixRoulottesBleuePeriodeHaute, false);

			$this->referentielRepository->setDateDebutPeriodeHauteRoulotte($this->parseDateSansAnnee($stdReglages->dateDebutPeriodeHauteRoulottes), false);
			$this->referentielRepository->setDebutAffichageTableauReservations($this->parseDateSansAnnee($stdReglages->dateDebutAffichageReservations), false);
			$this->referentielRepository->setFinAffichageTableauReservations($this->parseDateSansAnnee($stdReglages->dateFinAffichageReservations), true);

			$_SESSION["message_flash_statut"] = "success";
			$_SESSION["message_flash"] = "Enregistrement des réglages réussi";
		}
		catch (\Exception $ex) {
			$_SESSION["message_flash_statut"] = "error";
			$_SESSION["message_flash"] = "Une erreur inconnue est survenue lors "
			. "de l'enregistrement des réglages. Veuillez réessayer ou contacter "
			. "le support technique.";
		}

		//On redirige vers la page de consultation des r�servations
		header('Location: index.php');
	}

	/**
	 * Fonction d�coupant les donn�es concat�n�es dans la chaine pass�e en
	 * param�tre. Renvoie un tableau avec les donn�es
	 *
	 * @author adupuis
	 * @param string $strDonnees
	 *            Concat�nation des infos (le s�parateur est le |)
	 * @return Array Renvoie un tableau avec les donn�es pars�es
	 */
	private function parseInfosReservation($strDonnees) {
		$tabDonnees = explode("|", $strDonnees);
		$reservation = new Reservation();
		$client = new Client();

		if ($tabDonnees[0] == "v1.0") {
			// R�f�rence client
			$client->setReference($tabDonnees[1]);
			// R�f�rence r�servation
			$reservation->setReference($tabDonnees[2]);
			// Nom du client
			$client->setNom($tabDonnees[3]);
			// Pr�nom du client
			$client->setPrenom($tabDonnees[4]);
			// Rue du client
			$client->setAdresse1($tabDonnees[5]);
			// Compl�ment adresse du client
			$client->setAdresse2($tabDonnees[6]);
			// Code postal du client
			$client->setCodePostal($tabDonnees[7]);
			// Ville du client
			$client->setVille($tabDonnees[8]);
			// Pays du client
			$client->setPays($tabDonnees[9]);
			// Portable du client
			$client->setTelephonePortable($tabDonnees[10]);
			// Email du client
			$client->setEmail($tabDonnees[11]);
			// Piece d'identit� pr�sent�e
			$reservation->setPieceIdPresentee($tabDonnees[12]);
			// Date d'arriv�e
			$tabDateArrivee = explode("/", $tabDonnees[13]);
			$dateArrivee = new DateTime();
			$dateArrivee->setDate($tabDateArrivee[2], $tabDateArrivee[1], $tabDateArrivee[0]);
			$reservation->setDateArrivee($dateArrivee);
			// Date de d�part
			$tabDateDepart = explode("/", $tabDonnees[14]);
			$dateDepart = new DateTime();
			$dateDepart->setDate($tabDateDepart[2], $tabDateDepart[1], $tabDateDepart[0]);
			$reservation->setDateDepart($dateDepart);
			// Nombre d'adultes
			$reservation->setNombreAdultes($tabDonnees[15]);
			// Nombre d'enfants
			$reservation->setNombreEnfants($tabDonnees[16]);
			// Nombre d'animaux
			$reservation->setNombreAnimaux($tabDonnees[17]);
			// Nombre de petites tentes
			$reservation->setNombrePetitesTentes($tabDonnees[18]);
			// Nombre de grandes tentes
			$reservation->setNombreGrandesTentes($tabDonnees[19]);
			// Nombre de caravanes
			$reservation->setNombreCaravanes($tabDonnees[20]);
			// Nombre de vans
			$reservation->setNombreVans($tabDonnees[21]);
			// Nombre de camping cars
			$reservation->setNombreCampingCars($tabDonnees[22]);
			// Electricit�
			if ($tabDonnees[23] == "1") {
				$reservation->setElectricite(true);
			}
			else {
				$reservation->setElectricite(false);
			}
			// Nombre de nuit�s visiteur
			$reservation->setNombreNuitesVisiteur($tabDonnees[24]);
			// Observations
			$reservation->setObservations($tabDonnees[25]);
			// Arrhes
			$reservation->setArrhes($tabDonnees[27]);
			// Num�ro d'emplacement
			$reservation->setNumeroEmplacement($tabDonnees[28]);

			//On relie le client � la r�servation
			$reservation->setClient($client);
		}

		return $reservation;
	}

	/**
	 * Converti une chaine de caract�re au format DateTime
	 * @param string $date Date au format fran�ais sans ann�e. Exemple : 2 Avril
	 * @return \DateTime
	 */
	private function parseDateSansAnnee($date) {

		$date = str_ireplace("Janvier", "January",
		str_ireplace("F�vrier", "February",
		str_ireplace("Mars", "March",
		str_ireplace("Avril", "April",
		str_ireplace("Mai", "May",
		str_ireplace("Juin", "June",
		str_ireplace("Juillet", "July",
		str_ireplace("Ao�t", "August",
		str_ireplace("Septembre", "September",
		str_ireplace("Octobre", "October",
		str_ireplace("Novembre", "November",
		str_ireplace("D�cembre", "December", $date))))))))))));

		$retour = new \DateTime($date);

		return $retour;
	}

	/**
	 * Converti un DateTime en chaine de caract�re sans les ann�es
	 * @param \DateTime $date
	 * @return string Date au format fran�ais sans ann�e. Exemple : 2 Avril
	 */
	private function formatterDateSansAnnee(\DateTime $date = null) {
		$retour = null;

		if (!is_null($date)) {
			$retour = $date->format('j F');

			$retour = str_ireplace("January", "Janvier",
			str_ireplace("February", "F�vrier",
			str_ireplace("March", "Mars",
			str_ireplace("April", "Avril",
			str_ireplace("May", "Mai",
			str_ireplace("June", "Juin",
			str_ireplace("July", "Juillet",
			str_ireplace("August", "Ao�t",
			str_ireplace("September", "Septembre",
			str_ireplace("October", "Octobre",
			str_ireplace("November", "Novembre",
			str_ireplace("December", "D�cembre", $retour))))))))))));
		}

		return $retour;
	}
}

?>