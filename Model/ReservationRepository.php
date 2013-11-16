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
	 * Connexion � la base de donn�es
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
	 * Recherche une r�servation suivant sont id
	 * @author Arnaud DUPUIS
	 * @param integer $idReservation Id de la r�servation � rechercher
	 * @return Reservation Renvoie la r�servation si elle est trouv�e, null sinon
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
	 * Recherche une r�servation suivant sa r�f�rence
	 * @author Arnaud DUPUIS
	 * @param integer $refReservation R�f�rence de la r�servation � rechercher
	 * @return Reservation Renvoie la r�servation si elle est trouv�e, null sinon
	 */
	public function rechercherReservationParReference($refReservation) {
		$retour = null;

		if ($refReservation) {
			//Le premier caract�re (le R) n'est pas enregistr� en base
			if (strpos($refReservation, 'R') === 0) {
				$refReservation = substr($refReservation, 1);
			}
			$where = "r.reference = ' " . $refReservation . "'";

			$retour = $this->rechercherReservations($where);
		}

		return $retour;
	}

	/**
	 * Renvoie un tableau contenant toutes les r�servations de
	 * l'ann�e sp�cifi�e en param�tre
	 * @author Arnaud DUPUIS
	 * @param integer $annee Ann�e dont on veut toute les r�servations
	 * @param integer $mois Mois dont on veut toute les r�servations (optionel)
	 * @param integer $jour Jour dont on veut toute les r�servations (optionel)
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
	 * Enregistre une r�servation dans la base de donn�es
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation R�servation � enregistrer
	 * @return Reservation Renvoie la r�servation enregistr�e
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

		//On regarde si on doit faire une cr�ation ou une modification de r�servation
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
			//Cr�ation d'une r�servation
			$dateNow = new \DateTime();
			//On g�n�re une r�f�rence de r�servation
			$referenceMax = $this->rechercherReferenceMaxReservation($dateNow->format("Y"));
			//S'il existe une r�f�rence max pour l'ann�e en cours, on fait +1
			if (is_null($referenceMax)) {
				//S'il n'existe pas de r�f�rence max pour l'ann�e en cours, on en cr�e une
				$referenceReservation = $dateNow->format("y") . "0001";
			}
			else {
				$referenceReservation = intval($referenceMax) + 1;
			}

			$this->initBdd();
			//A la cr�ation des coordonn�es X et Y sont initialis�s � 0
			$sql = "INSERT INTO reservation (reference, id_client, date_arrivee, date_depart, "
				. "piece_id_presentee, arrhes, nombre_adultes, nombre_enfants, nombre_animaux, "
				. "nombre_petites_tentes, nombre_grandes_tentes, nombre_caravanes, "
				. "nombre_vans, nombre_camping_cars, electricite, nombre_nuitees_visiteur, "
				. "nombre_vehicules_supplementaires, roulotte_rouge, roulotte_bleue, "
				. "remise_exceptionnelle, observations, numero_emplacement, "
				. "coordonnees_x_emplacement, coordonnees_y_emplacement) "
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
				. "'" . intval($reservation->getNombreVehiculesSupplementaires()) . "', "
				. "'" . intval($reservation->getRoulotteRouge()) . "', "
				. "'" . intval($reservation->getRoulotteBleue()) . "', "
				. "'" . floatval($reservation->getRemiseExceptionnelle()) . "', "
				. "'" . $this->mysqli->real_escape_string($reservation->getObservations()) . "', "
				. "'" . intval($reservation->getNumeroEmplacement()) . "', "
				. "'0', "
				. "'0' "
				. ");";
		}
		else {
			$this->initBdd();
			//Modification de la r�servation
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
				. "r.nombre_vehicules_supplementaires = '" . intval($reservation->getNombreVehiculesSupplementaires()) . "', "
				. "r.roulotte_rouge = '" . intval($reservation->getRoulotteRouge()) . "', "
				. "r.roulotte_bleue = '" . intval($reservation->getRoulotteBleue()) . "', "
				. "r.remise_exceptionnelle = '" . floatval($reservation->getRemiseExceptionnelle()) . "', "
				. "r.observations = '" . $this->mysqli->real_escape_string($reservation->getObservations()) . "', "
				. "r.numero_emplacement = '" . intval($reservation->getNumeroEmplacement()) . "', "
				. "coordonnees_x_emplacement = '" . intval($reservation->getCoordonneesXEmplacement()) . "', "
				. "coordonnees_y_emplacement = '" . intval($reservation->getCoordonneesYEmplacement()) . "' "
				. " WHERE r.id = " . $reservation->getId();
		}

		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		if ($estCreationRes == true) {
			//On r�cup�re l'id inser�
			$reservation->setId($this->mysqli->insert_id);
		}

		$this->fermerBdd();

		//On recharge l'objet depuis la base
		$newReservation = $this->rechercherReservationParId($reservation->getId());
		return $newReservation[0];
	}

	/**
	 * Supprime une r�servation dans la base de donn�es
	 * @author Arnaud DUPUIS
	 * @param string $idReservation Id de la r�servation � supprimer
	 * @return Boolean Renvoie true si r�ussit, false sinon
	 */
	public function supprimerReservation($refReservation) {
		//Si la r�f�rence contient un R pour l'enl�ve pour la BDD
		if (strpos($refReservation, 'R') === 0) {
			$refReservation = substr($refReservation, 1);
		}

		$this->initBdd();
		
		//On supprime d'abord les factures pointant sur la r�servation
		$sql = "DELETE FROM facture WHERE facture.reference_reservation = '" . $this->mysqli->real_escape_string($refReservation) . "';";
		$result = $this->executerSQL($sql);
		
		//On envoie la requ�te de suppression
		$sql = "DELETE FROM reservation WHERE reservation.reference = '" . $this->mysqli->real_escape_string($refReservation) . "';";
		$result = $this->executerSQL($sql);

		$this->fermerBdd();

		return true;
	}

	/**
	 * Compte le nombre de r�servations totale ou par rapport � une ann�e
	 * @author Arnaud DUPUIS
	 * @param integer $annee Ann�e de recherche (si null, on compte toute les r�servations)
	 */
	public function compterNombreReservations($annee = null) {
		$retour = null;
		$sql = "SELECT COUNT(r.id) FROM reservation r";

		if (!is_null($annee)) {
			$sql .= " WHERE r.date_arrivee <= '" . $annee . "-12-31 00:00:00'"
					. " AND r.date_arrivee >= '" . $annee . "-01-01 00:00:00'";
		}

		$this->initBdd();

		//On envoie la requ�te de comptage
		$result = $this->executerSQL($sql);

		//Traitement des r�sultats
		while($data = $result->fetch_row()) {
			$retour = $data[0];
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Calcul le CA total pour les r�servations pass�es en param�tre
	 * @author Arnaud DUPUIS
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces r�servations
	 * @return float Renvoie le CA calcul�
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
				//On fait la somme de toute les prestations de la r�servation (sans compter les roulottes)
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
					$cAReservation += $prixVehiculeSupp * $reservation->getNombreVehiculesSupplementaires();
					$cAReservation += $prixVisiteur * $reservation->getNombreNuitesVisiteur();
					
					//On multiplie par le nombre de nuit�es
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
	 * @param array#Reservation $tabReservations Le calcul du CA se fait sur ces r�servations
	 * @return float Renvoie le CA calcul�
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
				//On fait la somme des roulottes (en suivant la p�riode haute ou basse)
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
				
				//On multiplie par le nombre de nuit�es
				$caRoulotte *= ($nbNuitees / 7);
				$cATotal += $caRoulotte;
			}
		}
		
		//On arroundi le CA � 2 chiffres apr�s la virgule
		$cATotal = (round($cATotal * 100) / 100);
	
		return $cATotal;
	}
	
	/**
	 * Retourne les diff�rents pays des visiteurs ayant une r�servation
	 * entre l'intervalle pass� en param�tre
	 * @author Arnaud DUPUIS
	 * @param DateTime $dateDebutInterval
	 * @param DateTime $dateFinInterval
	 * @return array Renvoie un tableau index� du type :
	 *   retour["pays"] = nb r�servations pour ce pays 
	 */
	public function recupererPaysClients(DateTime $dateDebutInterval, DateTime $dateFinInterval) {
		$retour = null;
		
		$this->initBdd();
		//Modification de la r�servation
		$sql = "SELECT COUNT(c.id), c.pays FROM reservation r " 
				. "LEFT JOIN client c ON r.id_client = c.id " 
				. "WHERE r.date_depart BETWEEN '" . $dateDebutInterval->format('Y-m-d H:i:s') . "' " 
						. "AND '" . $dateFinInterval->format('Y-m-d H:i:s') . "' " 
				. "GROUP BY c.pays;";
		
		//On envoie la requ�te
		$result = $this->executerSQL($sql);
		
		while($data = $result->fetch_row()) {
			$retour[$data[1]] = $data[0];
		}
		
		$this->fermerBdd();
		
		return $retour;
	}

	/**
	 * Fonction initialisant la connexion � la base de donn�es
	 */
	private function initBdd() {
		//On se connecte � MySQL
		$this->mysqli = new mysqli(Constante::SERVEUR_BDD, Constante::LOGIN_BDD, Constante::PASSWORD_BDD);

		//On s�lectionne la base de camping
		$this->mysqli->select_db(Constante::NOM_BASE_CAMPING);
	}

	/**
	 * Ex�cute la commande SQL pass�e en param�tre
	 * @param string $sql
	 * @return Renvoie le r�sultat de la requ�te
	 */
	private function executerSQL($sql) {
		$retour = $this->mysqli->query($sql) or die("Erreur SQL !\n" . $sql . "\n" . $this->mysqli->error);

		return $retour;
	}

	/**
	 * Fonction fermant la connexion � la base de donn�es
	 */
	private function fermerBdd() {
		//On ferme la connexion � MySQL
		$this->mysqli->close();
	}

	/**
	 * Effectue une recherche de r�servations (et clients rattach�s)
	 * @author Arnaud DUPUIS
	 * @param string $where Condition de la recherche. Si null, toute
	 *  les r�servations sont renvoy�es
	 * @return array#Reservation Renvoie un tableau d'objet Reservation
	 */
	private function rechercherReservations($where = null) {
		$retour = array();

		$this->initBdd();

		//Requ�te SQL pour r�cup�rer les r�servations
		$sql = 'SELECT r.id, r.reference, r.id_client, r.date_arrivee, r.date_depart, '
			. 'r.piece_id_presentee, r.arrhes, r.nombre_adultes, r.nombre_enfants, r.nombre_animaux, '
			. 'r.nombre_petites_tentes, r.nombre_grandes_tentes, r.nombre_caravanes, '
			. 'r.nombre_vans, r.nombre_camping_cars, r.electricite, r.nombre_nuitees_visiteur, '
			. 'r.nombre_vehicules_supplementaires, r.roulotte_rouge, r.roulotte_bleue, '
			. 'r.remise_exceptionnelle, r.observations, r.numero_emplacement, '
			. 'r.coordonnees_x_emplacement, r.coordonnees_y_emplacement, '
			. 'r.date_creation as date_creation_res, r.date_modification as date_modification_res '
			. 'FROM reservation r';
	
		if (!is_null($where)) {
			$sql .= ' WHERE ' . $where;
		}

		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		//On r�cup�re les r�sultats
		while($data = $result->fetch_row()) {
			//Cr�ation d'une nouvelle r�servation
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
			$newRes->setNombreVehiculesSupplementaires($data[17]);
			$roulotteRouge = false;
			if ($data[18] == "1" or $data[18] == true) {
				$roulotteRouge = true;
			}
			$newRes->setRoulotteRouge($roulotteRouge);
			$roulotteBleue = false;
			if ($data[19] == "1" or $data[19] == true) {
				$roulotteBleue = true;
			}
			$newRes->setRoulotteBleue($roulotteBleue);
			$newRes->setRemiseExceptionnelle($data[20]);
			$newRes->setObservations($data[21]);
			$newRes->setNumeroEmplacement($data[22]);
			$newRes->setCoordonneesXEmplacement($data[23]);
			$newRes->setCoordonneesYEmplacement($data[24]);
			$newRes->setDateCreation(new DateTime($data[25]));
			$newRes->setDateModification(new DateTime($data[26]));

			//R�cup�ration du client
			if ($data[2]) {
				$clients = $this->clientRepository->rechercherClientsCriteres(Array('id' => $data[2]));
				$newRes->setClient($clients[0]);
			}
			
			//R�cup�ration de la facture
			$factures = $this->factureRepository->rechercherFacture($data[1]);
			$newRes->setFacture($factures[0]);

			$retour[] = $newRes;
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Recherche la r�f�rence la plus �lev�e des r�servations � l'ann�e sp�cifi�e
	 * @author Arnaud DUPUIS
	 * @param integer $annee Ann�e de recherche (si null, recherche sur toute les reservations)
	 * @return Boolean Renvoie true si la r�f�rence existe, false sinon
	 */
	private function rechercherReferenceMaxReservation($annee = null) {
		$retour = null;
		$sql = "SELECT MAX(r.reference) FROM reservation r";

		if (!is_null($annee)) {
			$sql .= " WHERE r.date_arrivee <= '" . $annee . "-12-31 00:00:00'"
					. " AND r.date_arrivee >= '" . $annee . "-01-01 00:00:00'";
		}

		$this->initBdd();

		//On envoie la requ�te de comptage
		$result = $this->executerSQL($sql);

		//Traitement des r�sultats
		while($data = $result->fetch_row()) {
			$retour = $data[0];
		}

		$this->fermerBdd();

		return $retour;
	}
}

?>