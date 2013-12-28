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
	 * Tarif 1 (nombre x prix)
	 * @var string
	 */
	private $tarif1;
	
	/**
	 * Tarif 2 (nombre x prix)
	 * @var string
	 */
	private $tarif2;
	
	/**
	 * Tarif 3 (nombre x prix)
	 * @var string
	 */
	private $tarif3;
	
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
	 * Roulotte rouge période basse
	 * @var string
	 */
	private $roulotteRougePeriodeBasse;
	
	/**
	 * Roulotte rouge période haute
	 * @var string
	 */
	private $roulotteRougePeriodeHaute;
	
	/**
	 * Roulotte bleue période basse
	 * @var string
	 */
	private $roulotteBleuePeriodeBasse;
	
	/**
	 * Roulotte bleue période haute
	 * @var string
	 */
	private $roulotteBleuePeriodeHaute;
	
	/**
	 * Tente safari période basse
	 * @var string
	 */
	private $tenteSafariPeriodeBasse;
	
	/**
	 * Tente safari période haute
	 * @var string
	 */
	private $tenteSafariPeriodeHaute;
	
	/**
	 * Remise exceptionnelle sur la réservation
	 * @var float
	 */
	private $remiseExceptionnelle;

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
	 * Getter pour tarif 1
	 * @return string
	 */
	public function getTarif1() {
		return $this->tarif1;
	}
	
	/**
	 * Setter pour tarif 1
	 * @var string
	 */
	public function setTarif1($tarif1) {
		$this->tarif1 = $tarif1;
	}
	
	/**
	 * Getter pour tarif 2
	 * @return string
	 */
	public function getTarif2() {
		return $this->tarif2;
	}
	
	/**
	 * Setter pour tarif 2
	 * @var string
	 */
	public function setTarif2($tarif2) {
		$this->tarif2 = $tarif2;
	}
	
	/**
	 * Getter pour tarif 3
	 * @return string
	 */
	public function getTarif3() {
		return $this->tarif3;
	}
	
	/**
	 * Setter pour tarif 3
	 * @var string
	 */
	public function setTarif3($tarif3) {
		$this->tarif3 = $tarif3;
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
	 * Getter pour roulotte rouge période basse
	 * @return string
	 */
	public function getRoulotteRougePeriodeBasse() {
		return $this->roulotteRougePeriodeBasse;
	}
	
	/**
	 * Setter pour roulotte rouge période basse
	 * @var string
	 */
	public function setRoulotteRougePeriodeBasse($roulotteRougePeriodeBasse) {
		$this->roulotteRougePeriodeBasse = $roulotteRougePeriodeBasse;
	}
	
	/**
	 * Getter pour roulotte rouge période haute
	 * @return string
	 */
	public function getRoulotteRougePeriodeHaute() {
		return $this->roulotteRougePeriodeHaute;
	}
	
	/**
	 * Setter pour roulotte rouge période haute
	 * @var string
	 */
	public function setRoulotteRougePeriodeHaute($roulotteRougePeriodeHaute) {
		$this->roulotteRougePeriodeHaute = $roulotteRougePeriodeHaute;
	}
	
	/**
	 * Getter pour roulotte bleue période basse
	 * @return string
	 */
	public function getRoulotteBleuePeriodeBasse() {
		return $this->roulotteBleuePeriodeBasse;
	}
	
	/**
	 * Setter pour roulotte bleue période basse
	 * @var string
	 */
	public function setRoulotteBleuePeriodeBasse($roulotteBleuePeriodeBasse) {
		$this->roulotteBleuePeriodeBasse = $roulotteBleuePeriodeBasse;
	}
	
	/**
	 * Getter pour roulotte bleue période haute
	 * @return string
	 */
	public function getRoulotteBleuePeriodeHaute() {
		return $this->roulotteBleuePeriodeHaute;
	}
	
	/**
	 * Setter pour roulotte bleue période haute
	 * @var string
	 */
	public function setRoulotteBleuePeriodeHaute($roulotteBleuePeriodeHaute) {
		$this->roulotteBleuePeriodeHaute = $roulotteBleuePeriodeHaute;
	}
	
	/**
	 * Getter pour tente safari période basse
	 * @return string
	 */
	public function getTenteSafariPeriodeBasse() {
		return $this->tenteSafariPeriodeBasse;
	}
	
	/**
	 * Setter pour tente safari période basse
	 * @var string
	 */
	public function setTenteSafariPeriodeBasse($tenteSafariPeriodeBasse) {
		$this->tenteSafariPeriodeBasse = $tenteSafariPeriodeBasse;
	}
	
	/**
	 * Getter pour tente safari période haute
	 * @return string
	 */
	public function getTenteSafariPeriodeHaute() {
		return $this->tenteSafariPeriodeHaute;
	}
	
	/**
	 * Setter pour tente safari période haute
	 * @var string
	 */
	public function setTenteSafariPeriodeHaute($tenteSafariPeriodeHaute) {
		$this->tenteSafariPeriodeHaute = $tenteSafariPeriodeHaute;
	}
	
	/**
	 * Getter pour la remise exceptionnelle sur la réservation
	 * @return float
	 */
	public function getRemiseExceptionnelle() {
		return $this->remiseExceptionnelle;
	}
	
	/**
	 * Setter pour la remise exceptionnelle sur la réservation
	 * @var float
	 */
	public function setRemiseExceptionnelle($remiseExceptionnelle) {
		$this->remiseExceptionnelle = $remiseExceptionnelle;
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