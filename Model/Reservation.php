<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class Reservation {

	//Choix par le type de carte d'identit�
	const carteId = 'carteId';
	const autreId = 'autre';

	/**
	 * Identifiant de la r�servation
	 * @var integer
	 */
	private $id;

	/**
	 * R�f�rence de la r�servation
	 * @var string
	 */
	private $reference;

	/**
	 * Client ayant effectu� la r�servation
	 * @var Client
	 */
	private $client;

	/**
	 * Date d'arriv�e du client
	 * @var DateTime
	 */
	private $dateArrivee;

	/**
	 * Date de d�part du client
	 * @var DateTime
	 */
	private $dateDepart;

	/**
	 * Pi�ce d'identit� pr�sent�e (choix multiple)
	 * @var integer
	 */
	private $pieceIdPresentee;

	/**
	 * Nombre d'adultes sur l'emplacement
	 * @var integer
	 */
	private $nombreAdultes;

	/**
	 * Nombre d'enfants sur l'emplacement
	 * @var integer
	 */
	private $nombreEnfants;

	/**
	 * Nombre d'animaux sur l'emplacement
	 * @var integer
	 */
	private $nombreAnimaux;

	/**
	 * Nombre de petites tentes sur l'emplacement
	 * @var integer
	 */
	private $nombrePetitesTentes;

	/**
	 * Nombre de grandes tentes sur l'emplacement
	 * @var integer
	 */
	private $nombreGrandesTentes;

	/**
	 * Nombre de caravanes sur l'emplacement
	 * @var integer
	 */
	private $nombreCaravanes;

	/**
	 * Nombre de vans sur l'emplacement
	 * @var integer
	 */
	private $nombreVans;

	/**
	 * Nombre de camping cars sur l'emplacement
	 * @var integer
	 */
	private $nombreCampingCars;

	/**
	 * L'emplacement dispose de l'�lectricit�
	 * @var Boolean
	 */
	private $electricite;

	/**
	 * Nombre de nuit�s visiteur
	 * @var integer
	 */
	private $nombreNuitesVisiteur;
	
	/**
	 * Nombre de v�hicules suppl�mentaires
	 * @var integer
	 */
	private $nombreVehiculesSupplementaires;

	/**
	 * R�servation de la roulotte rouge
	 * @var boolean
	 */
	private $roulotteRouge;

	/**
	 * R�servation de la roulotte bleue
	 * @var boolean
	 */
	private $roulotteBleue;

	/**
	 * Observations sur la r�servation
	 * @var string
	 */
	private $observations;

	/**
	 * Arrhes sur la r�servation
	 * @var integer
	 */
	private $arrhes;

	/**
	 * Facture rattach�e � la r�servation
	 * @var string
	 */
	private $facture;

	/**
	 * Num�ro de l'emplacement
	 * @var integer
	 */
	private $numeroEmplacement;

	/**
	 * Coordonn�es X de l'emplacement
	 * @var integer
	 */
	private $coordonneesXEmplacement;

	/**
	 * Coordonn�es Y de l'emplacement
	 * @var integer
	 */
	private $coordonneesYEmplacement;

	/**
	 * Date de cr�ation de la r�servation
	 * @var DateTime
	 */
	private $dateCreation;

	/**
	 * Date de modification de la r�servation
	 * @var DateTime
	 */
	private $dateModification;

	/**
	 * Getter pour l'id
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Setter pour l'id
	 * @var integer
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Getter pour la r�f�rence
	 * @return string
	 */
	public function getReference() {
		return $this->reference;
	}

	/**
	 * Setter pour la r�f�rence
	 * @var string
	 */
	public function setReference($reference) {
		$this->reference = $reference;
	}

	/**
	 * Getter pour le client ayant effectu� la r�servation
	 * @return Client
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * Setter pour le client ayant effectu� la r�servation
	 * @var Client
	 */
	public function setClient(Client $client) {
		$this->client = $client;
	}

	/**
	 * Getter pour la date d'arriv�e du client
	 * @return DateTime
	 */
	public function getDateArrivee() {
		return $this->dateArrivee;
	}

	/**
	 * Setter pour la date d'arriv�e du client
	 * @var DateTime
	 */
	public function setDateArrivee(\DateTime $dateArrivee) {
		$this->dateArrivee = $dateArrivee;
	}

	/**
	 * Getter pour la date de d�part du client
	 * @return DateTime
	 */
	public function getDateDepart() {
		return $this->dateDepart;
	}

	/**
	 * Setter pour la date de d�part du client
	 * @var DateTime
	 */
	public function setDateDepart(\DateTime $dateDepart) {
		$this->dateDepart = $dateDepart;
	}

	/**
	 * Getter pour la pi�ce d'identit� pr�sent�e (choix multiple)
	 * @return string
	 */
	public function getPieceIdPresentee() {
		return $this->pieceIdPresentee;
	}

	/**
	 * Setter pour la pi�ce d'identit� pr�sent�e (choix multiple)
	 * @var string
	 */
	public function setPieceIdPresentee($pieceIdPresentee) {
		$this->pieceIdPresentee = $pieceIdPresentee;
	}

	/**
	 * Getter pour le nombre d'adultes sur l'emplacement
	 * @return integer
	 */
	public function getNombreAdultes() {
		return $this->nombreAdultes;
	}

	/**
	 * Setter pour le nombre d'adultes sur l'emplacement
	 * @var integer
	 */
	public function setNombreAdultes($nombreAdultes) {
		$this->nombreAdultes = $nombreAdultes;
	}

	/**
	 * Getter pour le nombre d'enfants sur l'emplacement
	 * @return integer
	 */
	public function getNombreEnfants() {
		return $this->nombreEnfants;
	}

	/**
	 * Setter pour le nombre d'enfants sur l'emplacement
	 * @var integer
	 */
	public function setNombreEnfants($nombreEnfants) {
		$this->nombreEnfants = $nombreEnfants;
	}

	/**
	 * Getter pour le nombre d'animaux sur l'emplacement
	 * @return integer
	 */
	public function getNombreAnimaux() {
		return $this->nombreAnimaux;
	}

	/**
	 * Setter pour le nombre d'animaux sur l'emplacement
	 * @var integer
	 */
	public function setNombreAnimaux($nombreAnimaux) {
		$this->nombreAnimaux = $nombreAnimaux;
	}

	/**
	 * Getter pour le nombre de petites tentes sur l'emplacement
	 * @return integer
	 */
	public function getNombrePetitesTentes() {
		return $this->nombrePetitesTentes;
	}

	/**
	 * Setter pour le nombre de petites tentes sur l'emplacement
	 * @var integer
	 */
	public function setNombrePetitesTentes($nombrePetitesTentes) {
		$this->nombrePetitesTentes = $nombrePetitesTentes;
	}

	/**
	 * Getter pour le nombre de grandes tentes sur l'emplacement
	 * @return integer
	 */
	public function getNombreGrandesTentes() {
		return $this->nombreGrandesTentes;
	}

	/**
	 * Setter pour le nombre de grandes tentes sur l'emplacement
	 * @var integer
	 */
	public function setNombreGrandesTentes($nombreGrandesTentes) {
		$this->nombreGrandesTentes = $nombreGrandesTentes;
	}

	/**
	 * Getter pour le nombre de caravanes sur l'emplacement
	 * @return integer
	 */
	public function getNombreCaravanes() {
		return $this->nombreCaravanes;
	}

	/**
	 * Setter pour le nombre de caravanes sur l'emplacement
	 * @var integer
	 */
	public function setNombreCaravanes($nombreCaravanes) {
		$this->nombreCaravanes = $nombreCaravanes;
	}

	/**
	 * Getter pour le nombre de vans sur l'emplacement
	 * @return integer
	 */
	public function getNombreVans() {
		return $this->nombreVans;
	}

	/**
	 * Setter pour le nombre de vans sur l'emplacement
	 * @var integer
	 */
	public function setNombreVans($nombreVans) {
		$this->nombreVans = $nombreVans;
	}

	/**
	 * Getter pour le nombre de camping cars sur l'emplacement
	 * @return integer
	 */
	public function getNombreCampingCars() {
		return $this->nombreCampingCars;
	}

	/**
	 * Setter pour le nombre de camping cars sur l'emplacement
	 * @var integer
	 */
	public function setNombreCampingCars($nombreCampingCars) {
		return $this->nombreCampingCars = $nombreCampingCars;
	}

	/**
	 * Getter pour l'�lectricit� sur l'emplacement
	 * @return Boolean
	 */
	public function getElectricite() {
		return $this->electricite;
	}

	/**
	 * Setter pour l'�lectricit� sur l'emplacement
	 * @var Boolean
	 */
	public function setElectricite($electricite) {
		$this->electricite = $electricite;
	}

	/**
	 * Getter pour le nombre de nuit�s visiteur
	 * @return integer
	 */
	public function getNombreNuitesVisiteur() {
		return $this->nombreNuitesVisiteur;
	}

	/**
	 * Setter pour le nombre de nuit�s visiteur
	 * @var integer
	 */
	public function setNombreNuitesVisiteur($nombreNuitesVisiteur) {
		$this->nombreNuitesVisiteur = $nombreNuitesVisiteur;
	}
	
	/**
	 * Getter pour le nombre de v�hicules suppl�mentaires
	 * @return integer
	 */
	public function getNombreVehiculesSupplementaires() {
		return $this->nombreVehiculesSupplementaires;
	}
	
	/**
	 * Setter pour le nombre de v�hicules suppl�mentaires
	 * @var integer
	 */
	public function setNombreVehiculesSupplementaires($nombreVehiculesSupplementaires) {
		$this->nombreVehiculesSupplementaires = $nombreVehiculesSupplementaires;
	}

	/**
	 * Getter pour la roulotte rouge
	 * @return boolean
	 */
	public function getRoulotteRouge() {
		return $this->roulotteRouge;
	}

	/**
	 * Setter pour la roulotte rouge
	 * @var boolean
	 */
	public function setRoulotteRouge($roulotteRouge) {
		$this->roulotteRouge = $roulotteRouge;
	}

	/**
	 * Getter pour la roulotte bleue
	 * @return boolean
	 */
	public function getRoulotteBleue() {
		return $this->roulotteBleue;
	}

	/**
	 * Setter pour la roulotte bleue
	 * @var boolean
	 */
	public function setRoulotteBleue($roulotteBleue) {
		$this->roulotteBleue = $roulotteBleue;
	}

	/**
	 * Getter pour les observations sur la r�servation
	 * @return string
	 */
	public function getObservations() {
		return $this->observations;
	}

	/**
	 * Setter pour les observations sur la r�servation
	 * @var string
	 */
	public function setObservations($observations) {
		$this->observations = $observations;
	}

	/**
	 * Getter pour les arrhes sur la r�servation
	 * @return float
	 */
	public function getArrhes() {
		return $this->arrhes;
	}

	/**
	 * Setter pour les arrhes sur la r�servation
	 * @var float
	 */
	public function setArrhes($arrhes) {
		$this->arrhes = $arrhes;
	}

	/**
	 * Getter pour la facture
	 * @return string
	 */
	public function getFacture() {
		return $this->facture;
	}

	/**
	 * Setter pour la facture
	 * @var string
	 */
	public function setFacture($facture) {
		$this->facture = $facture;
	}

	/**
	 * Getter pour le num�ro de l'emplacement
	 * @return integer
	 */
	public function getNumeroEmplacement() {
		return $this->numeroEmplacement;
	}

	/**
	 * Setter pour le num�ro de l'emplacement
	 * @var integer
	 */
	public function setNumeroEmplacement($numeroEmplacement) {
		$this->numeroEmplacement = $numeroEmplacement;
	}

	/**
	 * Getter pour les coordonn�es X de l'emplacement
	 * @return integer
	 */
	public function getCoordonneesXEmplacement() {
		return $this->coordonneesXEmplacement;
	}

	/**
	 * Setter pour les coordonn�es X de l'emplacement
	 * @var integer
	 */
	public function setCoordonneesXEmplacement($coordonneesXEmplacement) {
		$this->coordonneesXEmplacement = $coordonneesXEmplacement;
	}

	/**
	 * Getter pour les coordonn�es Y de l'emplacement
	 * @return integer
	 */
	public function getCoordonneesYEmplacement() {
		return $this->coordonneesYEmplacement;
	}

	/**
	 * Setter pour les coordonn�es Y de l'emplacement
	 * @var integer
	 */
	public function setCoordonneesYEmplacement($coordonneesYEmplacement) {
		$this->coordonneesYEmplacement = $coordonneesYEmplacement;
	}

	/**
	 * Getter pour la date de cr�ation de la r�servation
	 * @return DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}

	/**
	 * Setter pour la date de cr�ation de la r�servation
	 * @var DateTime
	 */
	public function setDateCreation(\DateTime $dateCreation) {
		$this->dateCreation = $dateCreation;
	}

	/**
	 * Getter pour la date de modification de la r�servation
	 * @return DateTime
	 */
	public function getDateModification() {
		return $this->dateModification;
	}

	/**
	 * Setter pour la date de modification de la r�servation
	 * @var DateTime
	 */
	public function setDateModification(\DateTime $dateModification) {
		$this->dateModification = $dateModification;
	}

}

?>