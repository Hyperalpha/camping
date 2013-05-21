<?php
include_once 'CommunModel.php';
include_once 'Constante.php';
include_once 'Referentiel.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class FactureRepository {

	/**
	 * Connexion � la base de donn�es
	 * @var unknown_type
	 */
	private $mysqli;

	public function __construct() {
		//Construction des singleton
	}

/**
	 * Recherche une facture en fonction de la r�f�rence de la r�servation pass�
	 * @author Arnaud DUPUIS
	 * @param string $referenceReservation R�f�rence de la r�servation rattach�e � la facture
	 * @return Facture Renvoie la facture trouv�e. Null sinon
	 */
	public function rechercherFacture($referenceReservation) {
		$retour = null;
		//On enl�ve le R de la r�servation
		$referenceReservation = ltrim($referenceReservation, 'R');
		
		$this->initBdd();

		//Requ�te SQL pour rechercher les clients
		if (!is_null($referenceReservation)) {
			$sql = 'SELECT f.id, f.reference_reservation, f.date_generation, f.devise, '
				. 'f.campeur_adulte, f.campeur_enfant, f.animal, f.petite_tente, f.van, '
				. 'f.grande_tente, f.caravane, f.camping_car, f.electricite, '
				. 'f.vehicule_supplementaire, f.nombre_visiteurs, '
				. 'f.date_creation as date_creation_facture, '
				. 'f.date_modification as date_modification_facture '
				. 'FROM facture f '
				. "WHERE f.reference_reservation = '" . $referenceReservation . "';";
		}

		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		//On r�cup�re les r�sultats
		while($data = $result->fetch_row()) {
			//Cr�ation de la facture
			$facture = new Facture();
			$facture->setId($data[0]);
			$facture->setReferenceReservation($data[1]);
			$facture->setDateGeneration(new DateTime($data[2]));
			$facture->setDevise($data[3]);
			$facture->setCampeurAdulte($data[4]);
			$facture->setCampeurEnfant($data[5]);
			$facture->setAnimal($data[6]);
			$facture->setPetiteTente($data[7]);
			$facture->setVan($data[8]);
			$facture->setGrandeTente($data[9]);
			$facture->setCaravane($data[10]);
			$facture->setCampingCar($data[11]);
			$facture->setElectricite($data[12]);
			$facture->setVehiculeSupplementaire($data[13]);
			$facture->setNombreVisiteurs($data[14]);
			$facture->setDateCreation(new DateTime($data[15]));
			$facture->setDateModification(new DateTime($data[16]));

			$retour[] = $facture;
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Enregistre une facture dans la base de donn�es (cr�ation ou modification)
	 * @author Arnaud DUPUIS
	 * @param Facture $facture Facture � enregistrer
	 * @return Facture Renvoie la facture enregistr�e
	 */
	public function enregistrerFacture(Facture $facture) {
		$referenceReservation = ltrim($facture->getReferenceReservation(), 'R');
		
		//On cherche si la facture existe d�j�
		$ancienneFacture = $this->rechercherFacture($facture->getReferenceReservation());
		
		$this->initBdd();
		
		//On regarde si on doit faire une cr�ation ou une modification
		if (is_null($ancienneFacture)) {
			//Cr�ation de la facture
			$sql = "INSERT INTO facture (id, reference_reservation, date_generation, devise, "
				. "campeur_adulte, campeur_enfant, animal, petite_tente, van, "
				. "grande_tente, caravane, camping_car, electricite, "
				. "vehicule_supplementaire, nombre_visiteurs, "
				. "roulotte_rouge_periode_basse, roulotte_rouge_periode_haute) "
				. "roulotte_bleue_periode_basse, roulotte_bleue_periode_haute) "
			. "VALUES ("
			. "'" . $this->mysqli->real_escape_string($facture->getId()) . "', "
			. "'" . $this->mysqli->real_escape_string($referenceReservation) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getDateGeneration()->format('Y-m-d H:i:s')) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getDevise()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getCampeurAdulte()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getCampeurEnfant()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getAnimal()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getPetiteTente()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getVan()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getGrandeTente()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getCaravane()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getCampingCar()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getElectricite()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getVehiculeSupplementaire()) . "', "
			. "'" . $this->mysqli->real_escape_string($facture->getNombreVisiteurs()) . "'"
			. "'" . $this->mysqli->real_escape_string($facture->getRoulotteRougePeriodeBasse()) . "'"
			. "'" . $this->mysqli->real_escape_string($facture->getRoulotteRougePeriodeHaute()) . "'"
			. "'" . $this->mysqli->real_escape_string($facture->getRoulotteBleuePeriodeBasse()) . "'"
			. "'" . $this->mysqli->real_escape_string($facture->getRoulotteBleuePeriodeHaute()) . "'"
			. ");";
		}
		else {
			//Modification d'un client
			$sql = "UPDATE facture f SET "
			. "f.id = '" . $this->mysqli->real_escape_string($facture->getId()) . "', "
			. "f.reference_reservation = '" . $this->mysqli->real_escape_string($referenceReservation)  . "', "
			. "f.date_generation = '" . $this->mysqli->real_escape_string($facture->getDateGeneration()->format('Y-m-d H:i:s')) . "', "
			. "f.devise = '" . $this->mysqli->real_escape_string($facture->getDevise()) . "', "
			. "f.campeur_adulte = '" . $this->mysqli->real_escape_string($facture->getCampeurAdulte()) . "', "
			. "f.campeur_enfant = '" . $this->mysqli->real_escape_string($facture->getCampeurEnfant()) . "', "
			. "f.animal = '" . $this->mysqli->real_escape_string($facture->getAnimal()) . "', "
			. "f.petite_tente = '" . $this->mysqli->real_escape_string($facture->getPetiteTente()) . "', "
			. "f.van = '" . $this->mysqli->real_escape_string($facture->getVan()) . "', "
			. "f.grande_tente = '" . $this->mysqli->real_escape_string($facture->getGrandeTente()) . "', "
			. "f.caravane = '" . $this->mysqli->real_escape_string($facture->getCaravane()) . "', "
			. "f.camping_car = '" . $this->mysqli->real_escape_string($facture->getCampingCar()) . "', "
			. "f.electricite = '" . $this->mysqli->real_escape_string($facture->getElectricite()) . "', "
			. "f.vehicule_supplementaire = '" . $this->mysqli->real_escape_string($facture->getVehiculeSupplementaire()) . "', "
			. "f.nombre_visiteurs = '" . $this->mysqli->real_escape_string($facture->getNombreVisiteurs()) . "', "
			. "f.roulotte_rouge_periode_basse = '" . $this->mysqli->real_escape_string($facture->getRoulotteRougePeriodeBasse()) . "', "
			. "f.roulotte_rouge_periode_haute = '" . $this->mysqli->real_escape_string($facture->getRoulotteRougePeriodeHaute()) . "', "
			. "f.roulotte_bleue_periode_basse = '" . $this->mysqli->real_escape_string($facture->getRoulotteBleuePeriodeBasse()) . "', "
			. "f.roulotte_bleue_periode_haute = '" . $this->mysqli->real_escape_string($facture->getRoulotteBleuePeriodeHaute()) . "' "
			. " WHERE f.id = '" . $ancienneFacture[0]->getId() . "'";
		}
		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		if (is_null($facture->getId())) {
			//On r�cup�re l'id inser�
			$facture->setId($this->mysqli->insert_id);
		}

		$this->fermerBdd();

		//On recharge l'objet depuis la base
		$newFacture = $this->rechercherFacture($facture->getReferenceReservation());
		return $newFacture[0];
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
}

?>