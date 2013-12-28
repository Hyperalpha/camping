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
	 * Exécute une recherche suivant les critères passés en paramètre
	 * @author Arnaud DUPUIS
	 * @param array $criteres Critères de recherche
	 * @return array Client Renvoie les clients renvoyés par la recherche
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
	 * Sérialise un objet Client pour pouvoir le passer à l'IHM
	 * @author Arnaud DUPUIS
	 * @param Client $client
	 * @return string Renvoie les infos sérialisées
	 */
	private function convertirClientPourIHM(Client $client) {
		$chaineRetour = "";
		$sep = "|";

		//Infos-Client
		//Version 1.0
		$chaineRetour .= "v1.0" . $sep;
		//Référence du client
		$chaineRetour .= $client->getReference() . $sep;
		//Nom
		$chaineRetour .= $client->getNom() . $sep;
		//Prénom
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
		//Date de création
		$chaineRetour .= $client->getDateCreation()->format('d/m/Y') . $sep;
		//Date de modification
		$chaineRetour .= $client->getDateModification()->format('d/m/Y') . $sep;

		return $chaineRetour;
	}

	/**
	 * Convertit un client au format attentdu par le module IHM d'autocomplétion
	 * @param Client $client
	 * @return stdClass Renvoie un stdClass avec les attributs value et label
	 */
	private function convertirClientPourAutoComplete(Client $client) {
		$retour = null;
		$stdClient = new \stdClass();

		//Concaténation des infos sur le client
		$stdClient->value = $this->convertirClientPourIHM($client);
		//Libellé affiché pour l'autocomplétion. De type "Prénom Nom (code postal)"
		$stdClient->label = $client->getPrenom() . " " . $client->getNom()
		. " (" . $client->getCodePostal() . ")";

		return $stdClient;
	}
}

?>