<?php
include_once 'Client.php';
include_once 'Constante.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class ClientRepository {

	/**
	 * Connexion � la base de donn�es
	 * @var unknown_type
	 */
	private $mysqli;

	/**
	 * Recherche des clients suivant des crit�res d�finis
	 * @author Arnaud DUPUIS
	 * @param array $criteres Tableau index� de crit�res de recherche
	 * @return array#Client Renvoie les clients trouv�s
	 */
	public function rechercherClientsCriteres($criteres) {
		$retour = array();
		$where = '';

		$this->initBdd();

		//Requ�te SQL pour rechercher les clients
		$sql = 'SELECT c.id, c.reference, c.nom, c.prenom, c.adresse1, '
		. 'c.code_postal, c.ville, c.pays, c.telephone, c.telephone_portable, c.email, '
		. 'c.date_creation as date_creation_client, '
		. 'c.date_modification as date_modification_client '
		. 'FROM client c';
		if (array_key_exists('id', $criteres) && $criteres['id']) {
			$where .= " c.id = '" . $criteres['id'] . "' AND";
		}
		if (array_key_exists('prenom', $criteres) && $criteres['prenom']) {
			$where .= " c.prenom LIKE '" . $criteres['prenom'] . "' AND";
		}
		if (array_key_exists('nom', $criteres) && $criteres['nom']) {
			$where .= " c.nom LIKE '" . $criteres['nom'] . "'";
		}
		if (array_key_exists('reference', $criteres) && $criteres['reference']) {
			$where .= " c.reference = '" . $criteres['reference'] . "'";
		}

		if ($where != '') {
			$where = trim($where, 'AND');
			$sql .= ' WHERE ' . $where;
		}

		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		//On r�cup�re les r�sultats
		while($data = $result->fetch_row()) {
			//Cr�ation du client
			$client = new Client();
			$client->setId($data[0]);
			$client->setReference($data[1]);
			$client->setNom($data[2]);
			$client->setPrenom($data[3]);
			$client->setAdresse1($data[4]);
			$client->setCodePostal($data[5]);
			$client->setVille($data[6]);
			$client->setPays($data[7]);
			$client->setTelephone($data[8]);
			$client->setTelephonePortable($data[9]);
			$client->setEmail($data[10]);
			$client->setDateCreation(new DateTime($data[11]));
			$client->setDateModification(new DateTime($data[12]));

			$retour[] = $client;
		}

		$this->fermerBdd();

		return $retour;
	}

	/**
	 * Enregistre un client dans la base de donn�es
	 * @author Arnaud DUPUIS
	 * @param Client $client Client � enregistrer
	 * @return Client Renvoie le client enregistr�
	 */
	public function enregistrerClient(Client $client) {
		$estCreationClient = false;

		//On regarde si on doit faire une cr�ation ou une modification de client
		if (is_null($client->getReference()) or $client->getReference() == '') {
			$estCreationClient = true;
		}
		else {
			$criteres["reference"] = $client->getReference();
			$resultIdClient = $this->rechercherClientsCriteres($criteres);
			if (is_null($resultIdClient) or count($resultIdClient) == 0) {
				$estCreationClient = true;
			}
			else {
				$client->setId($resultIdClient[0]->getId());
			}
		}

		$this->initBdd();
		if ($estCreationClient == true) {
			//Cr�ation d'un client
			$dateNow = new DateTime();
			//On g�n�re une r�f�rence client
			$referenceClient = "CLIENT" . $dateNow->format('YmdHis');
			$sql = "INSERT INTO client (reference, nom, prenom, adresse1, "
			. "code_postal, ville, pays, telephone, telephone_portable, "
			. "email) "
			. "VALUES ("
			. "'" . $this->mysqli->real_escape_string($referenceClient) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getNom()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getPrenom()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getAdresse1()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getCodePostal()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getVille()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getPays()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getTelephone()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getTelephonePortable()) . "', "
			. "'" . $this->mysqli->real_escape_string($client->getEmail()) . "'"
			. ");";
		}
		else {
			//Modification d'un client
			$sql = "UPDATE client c SET "
			. "c.reference = '" . $this->mysqli->real_escape_string($client->getReference()) . "', "
			. "c.nom = '" . $this->mysqli->real_escape_string($client->getNom())  . "', "
			. "c.prenom = '" . $this->mysqli->real_escape_string($client->getPrenom()) . "', "
			. "c.adresse1 = '" . $this->mysqli->real_escape_string($client->getAdresse1()) . "', "
			. "c.code_postal = '" . $this->mysqli->real_escape_string($client->getCodePostal()) . "', "
			. "c.ville = '" . $this->mysqli->real_escape_string($client->getVille()) . "', "
			. "c.pays = '" . $this->mysqli->real_escape_string($client->getPays()) . "', "
			. "c.telephone = '" . $this->mysqli->real_escape_string($client->getTelephone()) . "', "
			. "c.telephone_portable = '" . $this->mysqli->real_escape_string($client->getTelephonePortable()) . "', "
			. "c.email = '" . $this->mysqli->real_escape_string($client->getEmail()) . "' "
			. " WHERE c.id = " . $client->getId();
		}
		//On envoie la requ�te
		$result = $this->executerSQL($sql);

		if ($estCreationClient == true) {
			//On r�cup�re l'id inser�
			$client->setId($this->mysqli->insert_id);
		}

		$this->fermerBdd();
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