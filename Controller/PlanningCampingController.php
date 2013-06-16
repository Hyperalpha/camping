<?php
include_once '../Model/ExportExcelRepository.php';
include_once '../Model/ReferentielRepository.php';
include_once '../Model/ReservationRepository.php';
include_once '../Model/FactureRepository.php';
include_once '../Model/CommunModel.php';
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
	private $factureRepository;
	private $communModel;
	
	const DEFAULT_DATE_DEBUT = "15 June";
	const DEFAULT_DATE_FIN = "15 September";

	//Une erreur se déclanche si plus de x jours sont présents dans une année
	const WATCHDOG_JOURS_ANNEE = 370;
	
	const SEPARATEUR_RETOUR = '|';

	public function __construct() {
		//Construction des singleton
		$this->reservationRepository = new ReservationRepository();
		$this->exportExcelRepository = new ExportExcelRepository();
		$this->referentielRepository = new ReferentielRepository();
		$this->factureRepository = new FactureRepository();
		$this->communModel = new CommunModel();
	}

	/**
	 * Renvoie un tableau contenant toutes les réservations de l'année en cours
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau de réservation
	 */
	public function recupererToutesReservations() {
		$retour = array();

		//On récupère toute les réservations
		$annee = date("Y");
		$retour = $this->reservationRepository->rechercherToutesReservations($annee);

		return $retour;
	}

	/**
	 * Renvoie un tableau contenant toutes les réservations de la journée en cours
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau de réservation
	 */
	public function recupererReservationsDuJour() {
		$retour = array();

		//On récupère toute les réservations
		$annee = date("Y");
		$mois = date("m");
		$jour = date("d");
		$retour = $this->reservationRepository->rechercherToutesReservations($annee, $mois, $jour);
			
		return $retour;
	}

	/**
	 * Génération du calendrier de l'année
	 * @author Arnaud DUPUIS
	 * @return array Renvoie un tableau contenant les jours et les mois de l'année
	 */
	public function construireCalendrierReservations() {
		//Timestamp de début et de fin du tableau
		/**
		 * @TODO: reste un bug : si on change l'interval d'affichage, il n'est pas pris en compte directement. Il faut recharger la page//
		 */
		$dateDebut = $this->referentielRepository->getDebutAffichageTableauReservations();
		$dateFin = $this->referentielRepository->getFinAffichageTableauReservations();
		if (is_null($dateDebut)) {
			//Dates par défaut
			$dateDebut = new DateTime(self::DEFAULT_DATE_DEBUT);
		}
		if (is_null($dateFin)) {
			//Dates par défaut
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

			//Incrément 1 jour
			$timestpEnCours = strtotime('+1 day', $timestpEnCours);

			//Watchdog
			$i++;
		}

		return $tabCalendrier;
	}

	/**
	 * Calcul le CA total pour les réservations passées en paramètre
	 * @author Arnaud DUPUIS
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces réservations
	 * @return float Renvoie le CA calculé
	 */
	public function calculerCATotalReservations($tabReservations) {
		return $this->reservationRepository->calculerCATotalReservations($tabReservations);
	}
	
	/**
	 * Calcul le CA total des roulottes pour les réservations passées en paramètre
	 * @author Arnaud DUPUIS
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces réservations
	 * @return float Renvoie le CA calculé
	 */
	public function calculerCATotalRoulottes($tabReservations) {
		return $this->reservationRepository->calculerCATotalRoulottes($tabReservations);
	}
	
	/**
	 * Enregistre une réservation à partir de données sérialisées en entrée
	 * @author Arnaud DUPUIS
	 * @param string $strReservation
	 * @return integer Renvoie la référence de la réservation, la référence du
	 * client, le CA total Camping et le CA total roulottes (séparé pour des |). False sinon
	 */
	public function enregistrerReservation($strReservation) {
		$retour = false;

		try {
			$reservation = $this->parseInfosReservation($strReservation);
			$reservation = $this->reinitialiserChampsRoulotte($reservation);
			if ($reservation) {
				$newReservation = $this->reservationRepository->enregistrerReservation($reservation);
			}

			//Calcul des CA
			$reservations = $this->recupererToutesReservations();
			$caCamping = $this->calculerCATotalReservations($reservations);
			$caRoulottes = $this->calculerCATotalRoulottes($reservations);
			
			//Construction du retour
			$retour = $newReservation->getReference() . self::SEPARATEUR_RETOUR
					. $newReservation->getClient()->getReference() . self::SEPARATEUR_RETOUR
					. $caCamping . self::SEPARATEUR_RETOUR . $caRoulottes;
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Enregistre le numéro de l'emplacement d'une réservation
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

					//On enregistre les nouvelles coordonnées
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
	 * Enregistre les coordonnées de l'emplacement d'une réservation
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

					//On enregistre les nouvelles coordonnées
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
	 * Supprime une réservation à partir de l'id passé en entrée
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la réservation à supprimer
	 * @return Boolean Renvoie true si réussit, false sinon
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
	 * Sérialise un objet Reservation pour pouvoir le passer à l'IHM
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation
	 * @return string Renvoie les infos sérialisées
	 */
	public function convertirReservationPourIHM(Reservation $reservation) {
		$chaineRetour = "";
		$sep = self::SEPARATEUR_RETOUR;
		$client = $reservation->getClient();

		//Infos-Reservation
		//Version 1.0
		$chaineRetour .= "v1.0" . $sep;
		if (!is_null($client)) {
			//Référence du client
			$chaineRetour .= $client->getReference() . $sep;
		}
		else {
			$chaineRetour .= $sep;
		}
		//Référence réservation
		$chaineRetour .= $reservation->getReference() . $sep;
		if (!is_null($client)) {
			//Nom
			$chaineRetour .= $client->getNom() . $sep;
			//Prénom
			$chaineRetour .= $client->getPrenom() . $sep;
			//Rue
			$chaineRetour .= $client->getAdresse1() . $sep;
			//Complément adresse
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
		//Piece d'identité
		$piPresentee = $reservation->getPieceIdPresentee();

		switch ($piPresentee) {
			case Reservation::carteId:
				$strPi = "carteId";
				break;
			case Reservation::autreId:
				$strPi = "autre";
				break;
			default:
				$strPi = "";
				break;
		}
		$chaineRetour .= $strPi . $sep;
		//Date d'arrivée
		$chaineRetour .= $reservation->getDateArrivee()->format('d/m/Y') . $sep;
		//Date de départ
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
		//Electricité
		$chaineRetour .= $reservation->getElectricite() . $sep;
		//Nombre de nuités visiteur
		$chaineRetour .= intval($reservation->getNombreNuitesVisiteur()) . $sep;
		// Nombre de véhicules supplémentaires
		$chaineRetour .= intval($reservation->getNombreVehiculesSupplementaires()) . $sep;
		//Observations
		$chaineRetour .= $this->convertirZoneTextePourIhm($reservation->getObservations()) . $sep;
		//Id du bloc de réservation (non pris en charge par le PHP)
		$chaineRetour .= $sep;
		//Arrhes sur la réservation
		$chaineRetour .= $reservation->getArrhes() . $sep;
		// Numéro d'emplacement de la réservation
		$chaineRetour .= $reservation->getNumeroEmplacement() . $sep;
		// Roulotte rouge
		$chaineRetour .= $reservation->getRoulotteRouge() . $sep;
		// Roulotte bleue
		$chaineRetour .= $reservation->getRoulotteBleue() . $sep;
		// Référence facture
		$facture = $reservation->getFacture();
		if (!is_null($facture)) {
			$chaineRetour .= $facture->getId() . $sep;
		}
		else {
			$chaineRetour .= $sep;
		}

		return $chaineRetour;
	}

	/**
	 * Exporte la réservation précisée vers un fichier Word ou Excel.
	 * Redirige la page pour afficher le document
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la réservation à exporter
	 */
	public function exporterReservation($idReservation) {

		//On récupère la réservation
		$reservation = $this->reservationRepository->rechercherReservationParReference($idReservation);

		if (count($reservation) == 1) {
			$urlDocument = $this->exportExcelRepository->exporterReservation($reservation[0]);

			if ($urlDocument) {
				header('Location: ../Model/' . $urlDocument);
			}
		}
	}
	
	/**
	 * Exporte la facture de la réservation précisée vers un fichier Word ou Excel.
	 * Redirige la page pour afficher le document
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la réservation à exporter
	 * @param boolean $regenererFacture Doit-on regénèrer la facture ou afficher l'ancienne?
	 */
	public function exporterFacture($idReservation, $regenererFacture) {
	
		//On récupère la réservation
		$reservation = $this->reservationRepository->rechercherReservationParReference($idReservation);
	
		if (count($reservation) == 1) {
			//On exporte la facture
			$urlDocument = $this->exportExcelRepository->exporterFactureReservation($reservation[0], $regenererFacture);
	
			if ($urlDocument) {
				header('Location: ../Model/' . $urlDocument);
			}
		}
	}

	/**
	 * Charge la popup de réglages avec les valeurs du référentiel
	 * @author Arnaud DUPUIS
	 * @return string Renvoie la popup chargée
	 */
	public function chargerPopupReglages() {
		$retour = null;

		try {
			$retour = file_get_contents('../IHM/popup_reglages.html');

			//On remplit la popup avec les valeurs du référentiel
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
			$retour = str_replace('{{DATE_FIN_PERIODE_HAUTE_ROULOTTE}}', $this->formatterDateSansAnnee($this->referentielRepository->getDateFinPeriodeHauteRoulotte()), $retour);
			$retour = str_replace('{{DATE_DEBUT_TABLEAU_RESERVATIONS}}', $this->formatterDateSansAnnee($this->referentielRepository->getDebutAffichageTableauReservations()), $retour);
			$retour = str_replace('{{DATE_FIN_TABLEAU_RESERVATIONS}}', $this->formatterDateSansAnnee($this->referentielRepository->getFinAffichageTableauReservations()), $retour);
		}
		catch (\Exception $ex) {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Enregistre la popup de réglages avec les valeurs du référentiel
	 * @author Arnaud DUPUIS
	 * @param \stdClass $stdReglages Réglages à enregistrer sous forme de stdClass
	 * @return string Redirige vers la page de consultation des réservations
	 */
	public function enregistrerReglages($stdReglages) {
		session_start();

		//On set les valeurs sans mettre à jour la base de données
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
			$this->referentielRepository->setDateFinPeriodeHauteRoulotte($this->parseDateSansAnnee($stdReglages->dateFinPeriodeHauteRoulottes), false);
			$this->referentielRepository->setDebutAffichageTableauReservations($this->parseDateSansAnnee($stdReglages->dateDebutAffichageReservations), false);
			$this->referentielRepository->setFinAffichageTableauReservations($this->parseDateSansAnnee($stdReglages->dateFinAffichageReservations), true);

			$_SESSION["message_flash_statut"] = "success";
			$_SESSION["message_flash"] = "Enregistrement des réglages réussi";
		}
		catch (\Exception $ex) {
			var_dump($ex); die;
			$_SESSION["message_flash_statut"] = "error";
			$_SESSION["message_flash"] = "Une erreur inconnue est survenue lors "
					. "de l'enregistrement des réglages. Veuillez réessayer ou contacter "
							. "le support technique.";
		}

		//On redirige vers la page de consultation des réservations
		header('Location: index.php');
	}
	
	/**
	 * Renvoie le nombre de jours appartenant à la période basse et à la période haute
	 * 
	 * @author adupuis
	 * @param \DateTime $dateArrivee Date d'arrivée de la réservation
	 * @param \DateTime $dateDepart Date de départ de la réservation
	 * @param \DateTime $dateDebutPeriodeHaute Date de début de la période haute des roulottes
	 * @param \DateTime $dateFinPeriodeHaute Date de fin de la période haute des roulottes
	 * @return \stdClass Renvoie un objet stdClass avec les attributs nbJoursBas et nbJoursHaut
	 */
	public static function getNbJoursHautBasRoulottes(\DateTime $dateArrivee = null, 
			\DateTime $dateDepart = null, 
			\DateTime $dateDebutPeriodeHaute = null, 
			\DateTime $dateFinPeriodeHaute = null) {
		$retour = new \stdClass;
		$retour->nbJoursBas = 0;
		$retour->nbJoursHaut = 0;
		
		if (!is_null($dateArrivee) and !is_null($dateDepart) 
				and !is_null($dateDebutPeriodeHaute) and !is_null($dateFinPeriodeHaute)) {
			$interval = $dateDepart->diff($dateArrivee);
			$nbNuitees = intval($interval->format('%a'));
			$tspDateArrivee = $dateArrivee->getTimestamp();
			$tspDateDepart = $dateDepart->getTimestamp();
			$tspDateDebutPeriodeHaute = $dateDebutPeriodeHaute->getTimestamp();
			$tspDateFinPeriodeHaute = $dateFinPeriodeHaute->getTimestamp();
			
			if ((($tspDateArrivee < $tspDateDebutPeriodeHaute) 
				and ($tspDateDepart < $tspDateDebutPeriodeHaute)) or 
				(($tspDateArrivee > $tspDateFinPeriodeHaute) 
					and ($tspDateDepart > $tspDateFinPeriodeHaute))) {
				//Période basse
				$retour->nbJoursBas = $nbNuitees;
			}
			elseif (($tspDateArrivee >= $tspDateDebutPeriodeHaute) 
					and ($tspDateDepart <= $tspDateFinPeriodeHaute)) {
				//Période haute
				$retour->nbJoursHaut = $nbNuitees;
			}
			else {
				//A cheval sur la période basse et haute
				if (($tspDateArrivee < $tspDateDebutPeriodeHaute)
						and ($tspDateDepart <= $tspDateFinPeriodeHaute)) {
					//A cheval sur la date de début
					$intervalBas = $dateDebutPeriodeHaute->diff($dateArrivee);
					$intervalHaut = $dateDepart->diff($dateDebutPeriodeHaute);
					$retour->nbJoursBas = (intval($intervalBas->format('%a')) + 1);
					$retour->nbJoursHaut = intval($intervalHaut->format('%a'));
				}
				elseif (($tspDateArrivee >= $tspDateDebutPeriodeHaute)
						and ($tspDateDepart > $tspDateFinPeriodeHaute)) {
					//A cheval sur la date de fin
					$intervalBas = $dateDepart->diff($dateFinPeriodeHaute);
					$intervalHaut = $dateFinPeriodeHaute->diff($dateArrivee);
					$retour->nbJoursBas = intval($intervalBas->format('%a'));
					$retour->nbJoursHaut = (intval($intervalHaut->format('%a')) + 1);
				}
				else {
					//A cheval sur les deux dates
					$intervalBas1 = $dateDebutPeriodeHaute->diff($dateArrivee);
					$intervalHaut = $dateFinPeriodeHaute->diff($dateDebutPeriodeHaute);
					$intervalBas2 = $dateDepart->diff($dateFinPeriodeHaute);
					$retour->nbJoursBas = (intval($intervalBas1->format('%a')) + intval($intervalBas2->format('%a')));
					$retour->nbJoursHaut = (intval($intervalHaut->format('%a')) + 1);
				}
			}
		}
		
		return $retour;
	}
	
	/**
	 * 
	 * @author adupuis
	 */
	public function recupererPourcentagePaysClients() {
		$retourPourcentage = null;
		
		//On récupère les pays pour les réservation de l'année en cours
		$dateDebutInterval = new DateTime(date('Y') . '-01-01 00:00:00');
		$dateFinInterval = new DateTime(date('Y') . '-12-31 23:59:59');
		
		$paysClient = $this->reservationRepository->recupererPaysClients($dateDebutInterval,
			 $dateFinInterval);
		
		//Traitement des pays par clients
		if (!is_null($paysClient)) {
			//On calcule le nombre total de réservations
			$nbTotalResa = 0;
			foreach ($paysClient as $pays => $nbReservations) {
				$nbTotalResa += intval($nbReservations);
			}
			
			//On calcule les pourcentages
			foreach ($paysClient as $pays => $nbReservations) {
				$retourPourcentage[$pays] = (round(($nbReservations / $nbTotalResa) * 10000) / 100);
			}
		}
		
		return $retourPourcentage;
	}
	
	/**
	 * Exporte le contenu de la base de données à des fins de sauvegarde
	 * Redirige la page pour afficher le fichier
	 * @author adupuis
	 */
	public function exporterBaseDeDonnees() {
		
		//On va chercher le chemin vers mySQL dans le fichier de paramètres
		$parametersIni = parse_ini_file("../parameters.ini");
		
		if (array_key_exists('chemin_exec_mysqldump', $parametersIni)) {
			$urlFichierDump = $this->communModel->dumpBDD($parametersIni['chemin_exec_mysqldump']);
			
			if ($urlFichierDump) {
				header('Content-Type: application/force-download');
				header('Content-Disposition: attachment; filename="' . basename($urlFichierDump) . '"');
				readfile("../Model/$urlFichierDump" );
			}
		}
	}

	/**
	 * Fonction découpant les données concaténées dans la chaine passée en
	 * paramètre. Renvoie un tableau avec les données
	 *
	 * @author adupuis
	 * @param string $strDonnees
	 *            Concaténation des infos (le séparateur est le |)
	 * @return Array Renvoie un tableau avec les données parsées
	 */
	private function parseInfosReservation($strDonnees) {
		$tabDonnees = explode(self::SEPARATEUR_RETOUR, $strDonnees);
		$reservation = new Reservation();
		$client = new Client();

		//Infos-Reservation
		if ($tabDonnees[0] == "v1.0") {
			// Référence client
			$client->setReference($tabDonnees[1]);
			// Référence réservation
			$reservation->setReference($tabDonnees[2]);
			// Nom du client
			$client->setNom($tabDonnees[3]);
			// Prénom du client
			$client->setPrenom($tabDonnees[4]);
			// Rue du client
			$client->setAdresse1($tabDonnees[5]);
			// Complément adresse du client
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
			// Piece d'identité présentée
			$reservation->setPieceIdPresentee($tabDonnees[12]);
			// Date d'arrivée
			$tabDateArrivee = explode("/", $tabDonnees[13]);
			$dateArrivee = new DateTime();
			$dateArrivee->setDate($tabDateArrivee[2], $tabDateArrivee[1], $tabDateArrivee[0]);
			$reservation->setDateArrivee($dateArrivee);
			// Date de départ
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
			// Electricité
			if ($tabDonnees[23] == "1") {
				$reservation->setElectricite(true);
			}
			else {
				$reservation->setElectricite(false);
			}
			// Nombre de nuités visiteur
			$reservation->setNombreNuitesVisiteur($tabDonnees[24]);
			// Nombre de véhicules supplémentaires
			$reservation->setNombreVehiculesSupplementaires($tabDonnees[25]);
			// Observations
			$reservation->setObservations($tabDonnees[26]);
			// Arrhes
			$reservation->setArrhes($tabDonnees[28]);
			// Numéro d'emplacement
			$reservation->setNumeroEmplacement($tabDonnees[29]);
			// Roulotte rouge
			if ($tabDonnees[30] == "1") {
				$reservation->setRoulotteRouge(true);
			}
			else {
				$reservation->setRoulotteRouge(false);
			}
			// Roulotte bleue
			if ($tabDonnees[31] == "1") {
				$reservation->setRoulotteBleue(true);
			}
			else {
				$reservation->setRoulotteBleue(false);
			}
			// Référence facture
			$facture = $this->factureRepository->rechercherFacture($tabDonnees[2]);
			if (!is_null($facture)) {
				$reservation->setFacture($facture[0]);
			}

			//On relie le client à la réservation
			$reservation->setClient($client);
		}

		return $reservation;
	}
	
	/**
	 * Réinitialise des champs dans le cas d'une réservation avec roulotte
	 * @param Reservation $reservation
	 * @return Reservation Réservation mise à jour
	 */
	private function reinitialiserChampsRoulotte(Reservation $reservation) {
		
		//Si la réservation comporte une roulotte, on réinitialise les champs du camping
		if ($reservation->getRoulotteRouge() or $reservation->getRoulotteBleue()) {
			$reservation->setNombreAdultes(null);
			// Nombre d'enfants
			$reservation->setNombreEnfants(null);
			// Nombre d'animaux
			$reservation->setNombreAnimaux(null);
			// Nombre de petites tentes
			$reservation->setNombrePetitesTentes(null);
			// Nombre de grandes tentes
			$reservation->setNombreGrandesTentes(null);
			// Nombre de caravanes
			$reservation->setNombreCaravanes(null);
			// Nombre de vans
			$reservation->setNombreVans(null);
			// Nombre de camping cars
			$reservation->setNombreCampingCars(null);
			// Electricité
			$reservation->setElectricite(null);
			// Nombre de nuités visiteur
			$reservation->setNombreNuitesVisiteur(null);
			// Numéro d'emplacement
			$reservation->setNumeroEmplacement(null);
		}
		
		return $reservation;
	}

	/**
	 * Converti une chaine de caractère au format DateTime
	 * @param string $date Date au format français sans année. Exemple : 2 Avril
	 * @return \DateTime
	 */
	private function parseDateSansAnnee($date) {
		$retour = null;

		if (!is_null($date)) {
			$date = str_ireplace("Janvier", "January",
			str_ireplace("Février", "February",
			str_ireplace("Mars", "March",
			str_ireplace("Avril", "April",
			str_ireplace("Mai", "May",
			str_ireplace("Juin", "June",
			str_ireplace("Juillet", "July",
			str_ireplace("Août", "August",
			str_ireplace("Septembre", "September",
			str_ireplace("Octobre", "October",
			str_ireplace("Novembre", "November",
			str_ireplace("Décembre", "December", $date))))))))))));

			$retour = new \DateTime($date);
		}

		return $retour;
	}

	/**
	 * Converti un DateTime en chaine de caractère sans les années
	 * @param \DateTime $date
	 * @return string Date au format français sans année. Exemple : 2 Avril
	 */
	private function formatterDateSansAnnee(\DateTime $date = null) {
		$retour = null;

		if (!is_null($date)) {
			$retour = $date->format('j F');

			$retour = str_ireplace("January", "Janvier",
			str_ireplace("February", "Février",
			str_ireplace("March", "Mars",
			str_ireplace("April", "Avril",
			str_ireplace("May", "Mai",
			str_ireplace("June", "Juin",
			str_ireplace("July", "Juillet",
			str_ireplace("August", "Août",
			str_ireplace("September", "Septembre",
			str_ireplace("October", "Octobre",
			str_ireplace("November", "Novembre",
			str_ireplace("December", "Décembre", $retour))))))))))));
		}

		return $retour;
	}
	
	/**
	 * Convertit une zone de texte pour être compatible avec l'IHM
	 * (remplace par exemple les espaces par \n)
	 * @param string $valeur Contenu du champ texte
	 * @return string Renvoie la valeur formatée
	 */
	private function convertirZoneTextePourIhm($valeur) {
		$retour = null;
		
		//On remplace les retours chariot par des \n
		$retour = str_replace(CHR(13) . CHR(10), '\n', $valeur);
		$retour = str_replace(CHR(13), '\n', $retour);
		$retour = str_replace(CHR(10), '\n', $retour);
		
		return $retour;
	}
}

?>