<?php
include_once 'Reservation.php';
include_once 'Client.php';
include_once 'ClientRepository.php';
include_once 'Constante.php';
include_once 'ReferentielRepository.php';
include_once 'FactureRepository.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class ReservationRepository {

	/**
	 * Connexion à la base de données
	 * @var unknown_type
	 */
	private $mysqli;

	private $referentielRepository;

	private $clientRepository;
	
	private $factureRepository;

	public function __construct() {
		//Construction des singleton
		$this->referentielRepository = new ReferentielRepository();
		$this->clientRepository = new ClientRepository();
		$this->factureRepository = new FactureRepository();
	}

	/**
	 * Recherche une réservation suivant sont id
	 * @author Arnaud DUPUIS
	 * @param integer $idReservation Id de la réservation à rechercher
	 * @return Reservation Renvoie la réservation si elle est trouvée, null sinon
	 */
	public function rechercherReservationParId($idReservation) {
		$retour = null;

		if ($idReservation) {
			$where = "r.id = ' " . $idReservation . "'";

			$retour = $this->rechercherReservations($where);
		}

		return $retour;
	}

	/**
	 * Recherche une réservation suivant sa référence
	 * @author Arnaud DUPUIS
	 * @param integer $refReservation Référence de la réservation à rechercher
	 * @return Reservation Renvoie la réservation si elle est trouvée, null sinon
	 */
	public function rechercherReservationParReference($refReservation) {
		$retour = null;

		if ($refReservation) {
			//Le premier caractère (le R) n'est pas enregistré en base
			if (strpos($refReservation, 'R') === 0) {
				$refReservation = substr($refReservation, 1);
			}
			$where = "r.reference = ' " . $refReservation . "'";

			$retour = $this->rechercherReservations($where);
		}

		return $retour;
	}

	/**
	 * Renvoie un tableau contenant toutes les réservations de
	 * l'année spécifiée en paramètre
	 * @author Arnaud DUPUIS
	 * @param integer $annee Année dont on veut toute les réservations
	 * @param integer $mois Mois dont on veut toute les réservations (optionel)
	 * @param integer $jour Jour dont on veut toute les réservations (optionel)
	 * @return array#Reservation Renvoie un tableau d'objet Reservation
	 */
	public function rechercherToutesReservations($annee, $mois = null, $jour = null) {

		if (!is_null($mois) and !is_null($mois)) {
			$where = "(r.date_arrivee <= '" . $annee . "-" . $mois . "-" . $jour
			. " 23:59:59' AND  r.date_depart >= '"
					. $annee . "-" . $mois . "-" . $jour . " 00:00:00')";
		}
		else {
			$where = "(r.date_arrivee > '" . $annee . "-01-01 00:00:00' AND r.date_arrivee < '"
					. ($annee + 1) . "-01-01 00:00:00') OR (r.date_depart > '"
							. $annee . "-01-01 00:00:00' AND r.date_depart < '"
									. ($annee + 1) . "-01-01 00:00:00')";
		}

		return $this->rechercherReservations($where);
	}

	/**
	 * Enregistre une réservation dans la base de données
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation Réservation à enregistrer
	 * @return Reservation Renvoie la réservation enregistrée
	 */
	public function enregistrerReservation(Reservation $reservation) {
		$estCreationRes = false;
		$client = $reservation->getClient();

		//On enregistre d'abord le client
		$this->clientRepository->enregistrerClient($client);
		
		//On enregistre la facture
		$refFacture = null;
		if (!is_null($reservation->getFacture())) {
			$facture = $this->factureRepository->enregistrerFacture($reservation->getFacture());
			$refFacture = $facture->getId();
		}

		//On regarde si on doit faire une création ou une modification de réservation
		if (is_null($reservation->getReference()) or $reservation->getReference() == '') {
			$estCreationRes = true;
		}
		else {
			$resultIdRes = $this->rechercherReservationParReference($reservation->getReference());
			if (is_null($resultIdRes) or count($resultIdRes) == 0) {
				$estCreationRes = true;
			}
			else {
				$reservation->setId($resultIdRes[0]->getId());
			}
		}

		if ($estCreationRes == true) {
			//Création d'une réservation
			$dateNow = new \DateTime();
			//On génère une référence de réservation
			$referenceMax = $this->rechercherReferenceMaxReservation($dateNow->format("Y"));
			//S'il existe une référence max pour l'année en cours, on fait +1
			if (is_null($referenceMax)) {
				//S'il n'existe pas de référence max pour l'année en cours, on en crée une
				$referenceReservation = $dateNow->format("y") . "0001";
			}
			else {
				$referenceReservation = intval($referenceMax) + 1;
			}

			$this->initBdd();
			//A la création des coordonnées X et Y sont initialisés à 0
			$sql = "INSERT INTO reservation (reference, id_client, date_arrivee, date_depart, "
				. "piece_id_presentee, arrhes, nombre_adultes, nombre_enfants, nombre_animaux, "
				. "nombre_petites_tentes, nombre_grandes_tentes, nombre_caravanes, "
				. "nombre_vans, nombre_camping_cars, electricite, nombre_nuitees_visiteur, "
				. "roulotte_rouge, roulotte_bleue, observations, "
				. "numero_emplacement, coordonnees_x_emplacement, "
				. "coordonnees_y_emplacement) "
				. "VALUES ("
				. "'" . $referenceReservation. "', "
				. "'" . $reservation->getClient()->getId(). "', "
				. "'" . $reservation->getDateArrivee()->format('Y-m-d H:i:s') . "', "
				. "'" . $reservation->getDateDepart()->format('Y-m-d H:i:s') . "', "
				. "'" . $reservation->getPieceIdPresentee() . "', "
				. "'" . floatval($reservation->getArrhes()) . "', "
				. "'" . intval($reservation->getNombreAdultes()) . "', "
				. "'" . intval($reservation->getNombreEnfants()) . "', "
				. "'" . intval($reservation->getNombreAnimaux()) . "', "
				. "'" . intval($reservation->getNombrePetitesTentes()) . "', "
				. "'" . intval($reservation->getNombreGrandesTentes()) . "', "
				. "'" . intval($reservation->getNombreCaravanes()) . "', "
				. "'" . intval($reservation->getNombreVans()) . "', "
				. "'" . intval($reservation->getNombreCampingCars()) . "', "
				. "'" . $reservation->getElectricite() . "', "
				. "'" . intval($reservation->getNombreNuitesVisiteur()) . "', "
				. "'" . intval($reservation->getRoulotteRouge()) . "', "
				. "'" . intval($reservation->getRoulotteBleue()) . "', "
				. "'" . $this->mysqli->real_escape_string($reservation->getObservations()) . "', "
				. "'" . intval($reservation->getNumeroEmplacement()) . "', "
				. "'0', "
				. "'0' "
				. ");";
		}
		else {
			$this->initBdd();
			//Modification de la réservation
			$sql = "UPDATE reservation r SET "
				. "r.date_arrivee = '" . $reservation->getDateArrivee()->format('Y-m-d H:i:s')  . "', "
				. "r.date_depart = '" . $reservation->getDateDepart()->format('Y-m-d H:i:s')  . "', "
				. "r.piece_id_presentee = '" . $reservation->getPieceIdPresentee() . "', "
				. "r.arrhes = '" . floatval($reservation->getArrhes()) . "', "
				. "r.nombre_adultes = '" . intval($reservation->getNombreAdultes()) . "', "
				. "r.nombre_enfants = '" . intval($reservation->getNombreEnfants()) . "', "
				. "r.nombre_animaux = '" . intval($reservation->getNombreAnimaux()) . "', "
				. "r.nombre_petites_tentes = '" . intval($reservation->getNombrePetitesTentes()) . "', "
				. "r.nombre_grandes_tentes = '" . intval($reservation->getNombreGrandesTentes()) . "', "
				. "r.nombre_caravanes = '" . intval($reservation->getNombreCaravanes()) . "', "
				. "r.nombre_vans = '" . intval($reservation->getNombreVans()) . "', "
				. "r.nombre_camping_cars = '" . intval($reservation->getNombreCampingCars()) . "', "
				. "r.electricite = '" . $reservation->getElectricite() . "', "
				. "r.nombre_nuitees_visiteur = '" . intval($reservation->getNombreNuitesVisiteur()) . "', "
				. "r.roulotte_rouge = '" . intval($reservation->getRoulotteRouge()) . "', "
				. "r.roulotte_bleue = '" . intval($reservation->getRoulotteBleue()) . "', "
				. "r.observations = '" . $this->mysqli->real_escape_string($reservation->getObservations()) . "', "
				. "r.numero_emplacement = '" . intval($reservation->getNumeroEmplacement()) . "', "
				. "coordonnees_x_emplacement = '" . intval($reservation->getCoordonneesXEmplacement()) . "', "
				. "coordonnees_y_emplacement = '" . intval($reservation->getCoordonneesYEmplacement()) . "' "
				. " WHERE r.id = " . $reservation->getId();
		}

		//On envoie la requête
		$result = $this->executerSQL($sql);

		if ($estCreationRes == true) {
			//On récupère l'id inseré
			$reservation->setId($this->mysqli->insert_id);
		}

		$this->fermerBdd();

		//On recharge l'objet depuis la base
		$newReservation = $this->rechercherReservationParId($reservation->getId());
		return $newReservation[0];
	}

	/**
	 * Supprime une réservation dans la base de données
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la réservation à supprimer
	 * @return Boolean Renvoie true si réussit, false sinon
	 */
	public function supprimerReservation($refReservation) {
		//Si la référence contient un R pour l'enlève pour la BDD
		if (strpos($refReservation, 'R') === 0) {
			$refReservation = substr($refReservation, 1);
		}

		$this->initBdd();
		
		//On supprime d'abord les factures pointant sur la réservation
		$sql = "DELETE FROM facture WHERE facture.reference_reservation = '" . $this->mysqli->real_escape_string($refReservation) . "';";
		$result = $this->executerSQL($sql);
		
		//On envoie la requête de suppression
		$sql = "DELETE FROM reservation WHERE reservation.reference = '" . $this->mysqli->real_escape_string($refReservation) . "';";
		$result = $this->executerSQL($sql);

		$this->fermerBdd();

		return true;
	}

	/**
	 * Compte le nombre de réservations totale ou par rapport à une année
	 * @author Arnaud DUPUIS
	 * @param integer $annee Année de recherche (si null, on compte toute les réservations)
	 */
	public function compterNombreReservations($annee = null) {
		$retour = null;
		$sql = "SELECT COUNT(r.id) FROM reservation r";

		if (!is_null($annee)) {
			$sql .= " WHERE r.date_arrivee <= '" . $annee . "-12-31 00:00:00'"
					. " AND r.date_arrivee >= '" . $annee . "-01-01 00:00:00'";
		}

		$this->initBdd();

		//On envoie la requête de comptage
		$result = $this->executerSQL($sql);

		//Traitement des résultats
		while($data = $result->fetch_row()) {
			$retour = $data[0];
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Calcul le CA total pour les réservations passées en paramètre
	 * @author Arnaud DUPUIS
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces réservations
	 * @return float Renvoie le CA calculé
	 */
	public function calculerCATotalReservations($tabReservations) {
		$prixCampeurAdulte = $this->referentielRepository->getPrixCampeurAdulte();
		$prixCampeurEnfant = $this->referentielRepository->getPrixCampeurEnfant();
		$prixAnimal = $this->referentielRepository->getPrixAnimal();
		$prixPetitEmplacement = $this->referentielRepository->getPrixPetiteTenteVan();
		$prixGrandEmplacement = $this->referentielRepository->getPrixGrandeTenteCaravane();
		$prixCampingCar = $this->referentielRepository->getPrixCampingCar();
		$prixElectricite = $this->referentielRepository->getPrixElectricite();
		$prixVehiculeSupp = $this->referentielRepository->getPrixVehiculeSupp();
		$prixVisiteur = $this->referentielRepository->getPrixVisiteur();
		$cATotal = 0;
		$cAReservation = 0;

		if (!is_null($tabReservations)) {
			foreach ($tabReservations as $reservation) {
				//On fait la somme de toute les prestations de la réservation (sans compter les roulottes)
				if (($reservation->getRoulotteRouge() != true) and ($reservation->getRoulotteBleue() != true)) {
					$cAReservation = 0;
					$cAReservation += $prixCampeurAdulte * $reservation->getNombreAdultes();
					$cAReservation += $prixCampeurEnfant * $reservation->getNombreEnfants();
					$cAReservation += $prixAnimal * $reservation->getNombreAnimaux();
					$cAReservation += $prixPetitEmplacement * $reservation->getNombrePetitesTentes();
					$cAReservation += $prixPetitEmplacement * $reservation->getNombreVans();
					$cAReservation += $prixGrandEmplacement * $reservation->getNombreGrandesTentes();
					$cAReservation += $prixGrandEmplacement * $reservation->getNombreCaravanes();
					$cAReservation += $prixCampingCar * $reservation->getNombreCampingCars();
					$cAReservation += $prixElectricite * $reservation->getElectricite();
					/**
					 * @todo: nb de véhicules supp
					*/
					//$cAReservation += $prixVehiculeSupp * $reservation->getNombreEnfants();
					$cAReservation += $prixVisiteur * $reservation->getNombreNuitesVisiteur();
					
					//On multiplie par le nombre de nuitées
					$dateArrivee = $reservation->getDateArrivee();
					$dateDepart = $reservation->getDateDepart();
					$interval = $dateDepart->diff($dateArrivee);
					$nbNuitees = intval($interval->format('%a'));
					$cAReservation *= $nbNuitees;
					
					$cATotal += $cAReservation;
				}
			}
		}

		return $cATotal;
	}
	
	/**
	 * Calcul le CA total pour les roulottes
	 * @author Arnaud DUPUIS
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces réservations
	 * @return float Renvoie le CA calculé
	 */
	public function calculerCATotalRoulottes($tabReservations) {
		$prixRoulotteRougePeriodeBasse = $this->referentielRepository->getPrixRoulotteRougePeriodeBasse();
		$prixRoulotteRougePeriodeHaute = $this->referentielRepository->getPrixRoulotteRougePeriodeHaute();
		$prixRoulotteBleuePeriodeBasse = $this->referentielRepository->getPrixRoulotteBleuePeriodeBasse();
		$prixRoulotteBleuePeriodeHaute = $this->referentielRepository->getPrixRoulotteBleuePeriodeHaute();
		$dateDebutPeriodeHaute = $this->referentielRepository->getDateDebutPeriodeHauteRoulotte();
		$cATotal = 0;
		$cAReservation = 0;
		
		if (!is_null($tabReservations)) {
			foreach ($tabReservations as $reservation) {
				//On fait la somme des roulottes (en suivant la période haute ou basse)
				$caRoulotte = 0;
				$dateArrivee = $reservation->getDateArrivee();
				$dateDepart = $reservation->getDateDepart();
				$interval = $dateDepart->diff($dateArrivee);
				$nbNuitees = intval($interval->format('%a'));
				
				if ($reservation->getRoulotteRouge() == true) {
					if ($dateDepart->getTimestamp() < $dateDebutPeriodeHaute->getTimestamp()) {
						$caRoulotte += $prixRoulotteRougePeriodeBasse;
					}
					else {
						$caRoulotte += $prixRoulotteRougePeriodeHaute;
					}
				}
				if ($reservation->getRoulotteBleue() == true) {
					if ($dateDepart->getTimestamp() < $dateDebutPeriodeHaute->getTimestamp()) {
						$caRoulotte += $prixRoulotteBleuePeriodeBasse;
					}
					else {
						$caRoulotte += $prixRoulotteBleuePeriodeHaute;
					}
				}
				
				//On multiplie par le nombre de nuitées
				$caRoulotte *= ($nbNuitees / 7);
				$cATotal += $caRoulotte;
			}
		}
		
		//On arroundi le CA à 2 chiffres après la virgule
		$cATotal = (round($cATotal * 100) / 100);
	
		return $cATotal;
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
	private function executerSQL($sql) {
		$retour = $this->mysqli->query($sql) or die("Erreur SQL !\n" . $sql . "\n" . $this->mysqli->error);

		return $retour;
	}

	/**
	 * Fonction fermant la connexion à la base de données
	 */
	private function fermerBdd() {
		//On ferme la connexion à MySQL
		$this->mysqli->close();
	}

	/**
	 * Effectue une recherche de réservations (et clients rattachés)
	 * @author Arnaud DUPUIS
	 * @param string $where Condition de la recherche. Si null, toute
	 *  les réservations sont renvoyées
	 * @return array#Reservation Renvoie un tableau d'objet Reservation
	 */
	private function rechercherReservations($where = null) {
		$retour = array();

		$this->initBdd();

		//Requête SQL pour récupérer les réservations
		$sql = 'SELECT r.id, r.reference, r.id_client, r.date_arrivee, r.date_depart, '
			. 'r.piece_id_presentee, r.arrhes, r.nombre_adultes, r.nombre_enfants, r.nombre_animaux, '
			. 'r.nombre_petites_tentes, r.nombre_grandes_tentes, r.nombre_caravanes, '
			. 'r.nombre_vans, r.nombre_camping_cars, r.electricite, r.nombre_nuitees_visiteur, '
			. 'r.roulotte_rouge, r.roulotte_bleue, '
			. 'r.observations, r.numero_emplacement, '
			. 'r.coordonnees_x_emplacement, r.coordonnees_y_emplacement, '
			. 'r.date_creation as date_creation_res, r.date_modification as date_modification_res '
			. 'FROM reservation r';
	
		if (!is_null($where)) {
			$sql .= ' WHERE ' . $where;
		}

		//On envoie la requête
		$result = $this->executerSQL($sql);

		//On récupère les résultats
		while($data = $result->fetch_row()) {
			//Création d'une nouvelle réservation
			$newRes = new Reservation();
			$newRes->setId($data[0]);
			$newRes->setReference("R" . $data[1]);
			$newRes->setDateArrivee(new DateTime($data[3]));
			$newRes->setDateDepart(new DateTime($data[4]));
			$newRes->setPieceIdPresentee($data[5]);
			$newRes->setArrhes($data[6]);
			$newRes->setNombreAdultes($data[7]);
			$newRes->setNombreEnfants($data[8]);
			$newRes->setNombreAnimaux($data[9]);
			$newRes->setNombrePetitesTentes($data[10]);
			$newRes->setNombreGrandesTentes($data[11]);
			$newRes->setNombreCaravanes($data[12]);
			$newRes->setNombreVans($data[13]);
			$newRes->setNombreCampingCars($data[14]);
			$elec = false;
			if ($data[15] == "1" or $data[15] == true) {
				$elec = true;
			}
			$newRes->setElectricite($elec);
			$newRes->setNombreNuitesVisiteur($data[16]);
			$roulotteRouge = false;
			if ($data[17] == "1" or $data[17] == true) {
				$roulotteRouge = true;
			}
			$newRes->setRoulotteRouge($roulotteRouge);
			$roulotteBleue = false;
			if ($data[18] == "1" or $data[18] == true) {
				$roulotteBleue = true;
			}
			$newRes->setRoulotteBleue($roulotteBleue);
			$newRes->setObservations($data[19]);
			$newRes->setNumeroEmplacement($data[20]);
			$newRes->setCoordonneesXEmplacement($data[21]);
			$newRes->setCoordonneesYEmplacement($data[22]);
			$newRes->setDateCreation(new DateTime($data[23]));
			$newRes->setDateModification(new DateTime($data[24]));

			//Récupération du client
			if ($data[2]) {
				$clients = $this->clientRepository->rechercherClientsCriteres(Array('id' => $data[2]));
				$newRes->setClient($clients[0]);
			}
			
			//Récupération de la facture
			$factures = $this->factureRepository->rechercherFacture($data[1]);
			$newRes->setFacture($factures[0]);

			$retour[] = $newRes;
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Recherche la référence la plus élevée des réservations à l'année spécifiée
	 * @author Arnaud DUPUIS
	 * @param integer $annee Année de recherche (si null, recherche sur toute les reservations)
	 * @return Boolean Renvoie true si la référence existe, false sinon
	 */
	private function rechercherReferenceMaxReservation($annee = null) {
		$retour = null;
		$sql = "SELECT MAX(r.reference) FROM reservation r";

		if (!is_null($annee)) {
			$sql .= " WHERE r.date_arrivee <= '" . $annee . "-12-31 00:00:00'"
					. " AND r.date_arrivee >= '" . $annee . "-01-01 00:00:00'";
		}

		$this->initBdd();

		//On envoie la requête de comptage
		$result = $this->executerSQL($sql);

		//Traitement des résultats
		while($data = $result->fetch_row()) {
			$retour = $data[0];
		}

		$this->fermerBdd();

		return $retour;
	}
}

?>