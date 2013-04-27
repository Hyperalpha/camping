<?php
include_once 'Reservation.php';
include_once 'Client.php';
include_once 'ClientRepository.php';
include_once 'Constante.php';

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

	private $clientRepository;

	public function __construct() {
		//Construction des singleton
		$this->clientRepository = new ClientRepository();
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
			. "observations, numero_emplacement, coordonnees_x_emplacement, "
			. "coordonnees_y_emplacement) "
			. "VALUES ("
			. "'" . $referenceReservation. "', "
			. "'" . $reservation->getClient()->getId(). "', "
			. "'" . $reservation->getDateArrivee()->format('Y-m-d H:i:s') . "', "
			. "'" . $reservation->getDateDepart()->format('Y-m-d H:i:s') . "', "
			. "'" . $reservation->getPieceIdPresentee() . "', "
			. "'" . intval($reservation->getArrhes()) . "', "
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
			. "r.arrhes = '" . intval($reservation->getArrhes()) . "', "
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

		$sql = "DELETE FROM reservation WHERE reservation.reference = '" . $refReservation . "';";

		$this->initBdd();

		//On envoie la requête de suppression
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
		. 'r.observations, r.reference_facture, r.numero_emplacement, '
		. 'r.coordonnees_x_emplacement, r.coordonnees_y_emplacement, '
		. 'r.date_creation as date_creation_res, r.date_modification as date_modification_res, '
		. 'c.reference, c.nom, c.prenom, c.adresse1, c.adresse2, c.code_postal, '
		. 'c.ville, c.pays, c.telephone, c.telephone_portable, c.email, '
		. 'c.date_creation as date_creation_client, '
		. 'c.date_modification as date_modification_client '
		. 'FROM reservation r LEFT JOIN client c ON r.id_client = c.id';

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
			$newRes->setNombreNuitesVisiteur($data[15]);
			$elec = false;
			if ($data[16] == "1" or $data[16] == true) {
				$elec = true;
			}
			$newRes->setElectricite($elec);
			$newRes->setObservations($data[17]);
			//$newRes->setReferenceFacture($referenceFacture);
			$newRes->setNumeroEmplacement($data[19]);
			$newRes->setCoordonneesXEmplacement($data[20]);
			$newRes->setCoordonneesYEmplacement($data[21]);
			$newRes->setDateCreation(new DateTime($data[22]));
			$newRes->setDateModification(new DateTime($data[23]));

			//Création du client
			if ($data[2]) {
				$client = new Client();
				$client->setId($data[2]);
				$client->setReference($data[24]);
				$client->setNom($data[25]);
				$client->setPrenom($data[26]);
				$client->setAdresse1($data[27]);
				$client->setAdresse2($data[28]);
				$client->setCodePostal($data[29]);
				$client->setVille($data[30]);
				$client->setPays($data[31]);
				$client->setTelephone($data[32]);
				$client->setTelephonePortable($data[33]);
				$client->setEmail($data[34]);
				$client->setDateCreation(new DateTime($data[35]));
				$client->setDateModification(new DateTime($data[36]));
				$newRes->setClient($client);
			}
				
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