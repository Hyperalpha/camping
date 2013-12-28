<?php
include_once '../Model/ClientRepository.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class RechercherClientController {

	private $clientRepository;

	public function __construct() {
		//Construction des singleton
		$this->clientRepository = new ClientRepository();
	}

	/**
	 * Ex�cute une recherche suivant les crit�res pass�s en param�tre
	 * @author Arnaud DUPUIS
	 * @param array $criteres Crit�res de recherche
	 * @return array Client Renvoie les clients renvoy�s par la recherche
	 */
	public function rechercherClient($criteres) {
		$retour = array();

		$tabClients = $this->clientRepository->rechercherClientsCriteres($criteres);

		if ($tabClients) {
			foreach ($tabClients as $client) {
				$retour[] = $this->convertirClientPourAutoComplete($client);
			}
		}

		return $retour;
	}

	/**
	 * S�rialise un objet Client pour pouvoir le passer � l'IHM
	 * @author Arnaud DUPUIS
	 * @param Client $client
	 * @return string Renvoie les infos s�rialis�es
	 */
	private function convertirClientPourIHM(Client $client) {
		$chaineRetour = "";
		$sep = "|";

		//Infos-Client
		//Version 1.0
		$chaineRetour .= "v1.0" . $sep;
		//R�f�rence du client
		$chaineRetour .= $client->getReference() . $sep;
		//Nom
		$chaineRetour .= $client->getNom() . $sep;
		//Pr�nom
		$chaineRetour .= $client->getPrenom() . $sep;
		//Rue
		$chaineRetour .= $client->getAdresse1() . $sep;
		//Code postal
		$chaineRetour .= $client->getCodePostal() . $sep;
		//Ville
		$chaineRetour .= $client->getVille() . $sep;
		//Pays
		$chaineRetour .= $client->getPays() . $sep;
		//Telephone
		$chaineRetour .= $client->getTelephone() . $sep;
		//Portable
		$chaineRetour .= $client->getTelephonePortable() . $sep;
		//Email
		$chaineRetour .= $client->getEmail() . $sep;
		//Date de cr�ation
		$chaineRetour .= $client->getDateCreation()->format('d/m/Y') . $sep;
		//Date de modification
		$chaineRetour .= $client->getDateModification()->format('d/m/Y') . $sep;

		return $chaineRetour;
	}

	/**
	 * Convertit un client au format attentdu par le module IHM d'autocompl�tion
	 * @param Client $client
	 * @return stdClass Renvoie un stdClass avec les attributs value et label
	 */
	private function convertirClientPourAutoComplete(Client $client) {
		$retour = null;
		$stdClient = new \stdClass();

		//Concat�nation des infos sur le client
		$stdClient->value = $this->convertirClientPourIHM($client);
		//Libell� affich� pour l'autocompl�tion. De type "Pr�nom Nom (code postal)"
		$stdClient->label = $client->getPrenom() . " " . $client->getNom()
		. " (" . $client->getCodePostal() . ")";

		return $stdClient;
	}
}

?>