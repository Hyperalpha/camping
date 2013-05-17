<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class Facture {
	/**
	 * Identifiant de la facture
	 * @var string
	 */
	private $id;

	/**
	 * Référence de la réservation
	 * @var string
	 */
	private $referenceReservation;

	/**
	 * Date de génération de la facture
	 * @var DateTime
	 */
	private $dateGeneration;
	
	/**
	 * Devise de la facture (€)
	 * @var string
	 */
	private $devise;
	
	/**
	 * Campeur adulte (nombre x prix)
	 * @var string
	 */
	private $campeurAdulte;
	
	/**
	 * Campeur enfant (nombre x prix)
	 * @var string
	 */
	private $campeurEnfant;
	
	/**
	 * Animal (nombre x prix)
	 * @var string
	 */
	private $animal;
	
	/**
	 * Petite tente (nombre x prix)
	 * @var string
	 */
	private $petiteTente;
	
	/**
	 * Van (nombre x prix)
	 * @var string
	 */
	private $van;
	
	/**
	 * Grande tente (nombre x prix)
	 * @var string
	 */
	private $grandeTente;
	
	/**
	 * Caravane (nombre x prix)
	 * @var string
	 */
	private $caravane;
	
	/**
	 * Camping car (nombre x prix)
	 * @var string
	 */
	private $campingCar;
	
	/**
	 * Electricité (nombre x prix)
	 * @var string
	 */
	private $electricite;
	
	/**
	 * Véhicule supplémentaire (nombre x prix)
	 * @var string
	 */
	private $vehiculeSupplementaire;
	
	/**
	 * Nombre de visiteurs (nombre x prix)
	 * @var string
	 */
	private $nombreVisiteurs;

	/**
	 * Date de création du client
	 * @var DateTime
	 */
	private $dateCreation;

	/**
	 * Date de modification du client
	 * @var DateTime
	 */
	private $dateModification;

	/**
	 * Getter pour l'id
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Setter pour l'id
	 * @var string
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Getter pour la référence de la réservation
	 * @return string
	 */
	public function getReferenceReservation() {
		return $this->referenceReservation;
	}

	/**
	 * Setter pour la référence de la réservation
	 * @var string
	 */
	public function setReferenceReservation($referenceReservation) {
		$this->referenceReservation = $referenceReservation;
	}
	
	/**
	 * Getter pour la date de génération de la facture
	 * @return DateTime
	 */
	public function getDateGeneration() {
		return $this->dateGeneration;
	}
	
	/**
	 * Setter pour la date de génération de la facture
	 * @var DateTime
	 */
	public function setDateGeneration($dateGeneration) {
		$this->dateGeneration = $dateGeneration;
	}
	
	/**
	 * Getter pour la devise
	 * @return string
	 */
	public function getDevise() {
		return $this->devise;
	}
	
	/**
	 * Setter pour la devise
	 * @var string
	 */
	public function setDevise($devise) {
		$this->devise = $devise;
	}
	
	/**
	 * Getter pour campeur adulte
	 * @return string
	 */
	public function getCampeurAdulte() {
		return $this->campeurAdulte;
	}
	
	/**
	 * Setter pour campeur adulte
	 * @var string
	 */
	public function setCampeurAdulte($campeurAdulte) {
		$this->campeurAdulte = $campeurAdulte;
	}
	
	/**
	 * Getter pour campeur enfant
	 * @return string
	 */
	public function getCampeurEnfant() {
		return $this->campeurEnfant;
	}
	
	/**
	 * Setter pour campeur enfant
	 * @var string
	 */
	public function setCampeurEnfant($campeurEnfant) {
		$this->campeurEnfant = $campeurEnfant;
	}
	
	/**
	 * Getter pour animal
	 * @return string
	 */
	public function getAnimal() {
		return $this->animal;
	}
	
	/**
	 * Setter pour animal
	 * @var string
	 */
	public function setAnimal($animal) {
		$this->animal = $animal;
	}
	
	/**
	 * Getter pour petite tente
	 * @return string
	 */
	public function getPetiteTente() {
		return $this->petiteTente;
	}
	
	/**
	 * Setter pour petite tente
	 * @var string
	 */
	public function setPetiteTente($petiteTente) {
		$this->petiteTente = $petiteTente;
	}
	
	/**
	 * Getter pour van
	 * @return string
	 */
	public function getVan() {
		return $this->van;
	}
	
	/**
	 * Setter pour van
	 * @var string
	 */
	public function setVan($van) {
		$this->van = $van;
	}
	
	/**
	 * Getter pour grande tente
	 * @return string
	 */
	public function getGrandeTente() {
		return $this->grandeTente;
	}
	
	/**
	 * Setter pour grande tente
	 * @var string
	 */
	public function setGrandeTente($grandeTente) {
		$this->grandeTente = $grandeTente;
	}
	
	/**
	 * Getter pour caravane
	 * @return string
	 */
	public function getCaravane() {
		return $this->caravane;
	}
	
	/**
	 * Setter pour caravane
	 * @var string
	 */
	public function setCaravane($caravane) {
		$this->caravane = $caravane;
	}
	
	/**
	 * Getter pour camping-car
	 * @return string
	 */
	public function getCampingCar() {
		return $this->campingCar;
	}
	
	/**
	 * Setter pour camping-car
	 * @var string
	 */
	public function setCampingCar($campingCar) {
		$this->campingCar = $campingCar;
	}
	
	/**
	 * Getter pour électricité
	 * @return string
	 */
	public function getElectricite() {
		return $this->electricite;
	}
	
	/**
	 * Setter pour électricité
	 * @var string
	 */
	public function setElectricite($electricite) {
		$this->electricite = $electricite;
	}
	
	/**
	 * Getter pour nombre de véhicules supplémentaires
	 * @return string
	 */
	public function getVehiculeSupplementaire() {
		return $this->vehiculeSupplementaire;
	}
	
	/**
	 * Setter pour nombre de véhicules supplémentaires
	 * @var string
	 */
	public function setVehiculeSupplementaire($vehiculeSupplementaire) {
		$this->vehiculeSupplementaire = $vehiculeSupplementaire;
	}
	
	/**
	 * Getter pour nombre de visiteurs
	 * @return string
	 */
	public function getNombreVisiteurs() {
		return $this->nombreVisiteurs;
	}
	
	/**
	 * Setter pour nombre de visiteurs
	 * @var string
	 */
	public function setNombreVisiteurs($nombreVisiteurs) {
		$this->nombreVisiteurs = $nombreVisiteurs;
	}
	
	/**
	 * Getter pour la date de création du client
	 * @return DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}

	/**
	 * Setter pour la date de création du client
	 * @var DateTime
	 */
	public function setDateCreation(\DateTime $dateCreation) {
		$this->dateCreation = $dateCreation;
	}

	/**
	 * Getter pour la date de modification du client
	 * @return DateTime
	 */
	public function getDateModification() {
		return $this->dateModification;
	}

	/**
	 * Setter pour la date de modification du client
	 * @var DateTime
	 */
	public function setDateModification(\DateTime $dateModification) {
		$this->dateModification = $dateModification;
	}

}

?>