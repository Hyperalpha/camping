<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class Client {
	/**
	 * Identifiant du client
	 * @var integer
	 */
	private $id;

	/**
	 * Référence du client
	 * @var string
	 */
	private $reference;

	/**
	 * Nom du client
	 * @var string
	 */
	private $nom;

	/**
	 * Prénom du client
	 * @var string
	 */
	private $prenom;

	/**
	 * Adresse du client
	 * @var string
	 */
	private $adresse1;

	/**
	 * Complément d'adresse du client
	 * @var string
	 */
	private $adresse2;

	/**
	 * Code postal du client
	 * @var string
	 */
	private $codePostal;

	/**
	 * Ville du client
	 * @var string
	 */
	private $ville;

	/**
	 * Pays du client
	 * @var string
	 */
	private $pays;

	/**
	 * Numéro de téléphone du client
	 * @var string
	 */
	private $telephone;

	/**
	 * Numéro de téléphone portable du client
	 * @var string
	 */
	private $telephonePortable;

	/**
	 * Email du client
	 * @var string
	 */
	private $email;

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
	 * Getter pour la référence du client
	 * @return string
	 */
	public function getReference() {
		return $this->reference;
	}

	/**
	 * Setter pour la référence du client
	 * @var string
	 */
	public function setReference($reference) {
		$this->reference = $reference;
	}

	/**
	 * Getter pour le nom du client
	 * @return string
	 */
	public function getNom() {
		return $this->nom;
	}

	/**
	 * Setter pour le nom du client
	 * @var string
	 */
	public function setNom($nom) {
		$this->nom = $nom;
	}

	/**
	 * Getter pour le prénom du client
	 * @return string
	 */
	public function getPrenom() {
		return $this->prenom;
	}

	/**
	 * Setter pour le prénom du client
	 * @var string
	 */
	public function setPrenom($prenom) {
		$this->prenom = $prenom;
	}

	/**
	 * Getter pour l'adresse du client
	 * @return string
	 */
	public function getAdresse1() {
		return $this->adresse1;
	}

	/**
	 * Setter pour l'adresse du client
	 * @var string
	 */
	public function setAdresse1($adresse1) {
		$this->adresse1 = $adresse1;
	}

	/**
	 * Getter pour le complément d'adresse du client
	 * @return string
	 */
	public function getAdresse2() {
		return $this->adresse2;
	}

	/**
	 * Setter pour le complément d'adresse du client
	 * @var string
	 */
	public function setAdresse2($adresse2) {
		$this->adresse2 = $adresse2;
	}

	/**
	 * Getter pour le code postal du client
	 * @return string
	 */
	public function getCodePostal() {
		return $this->codePostal;
	}

	/**
	 * Setter pour le code postal du client
	 * @var string
	 */
	public function setCodePostal($codePostal) {
		$this->codePostal = $codePostal;
	}

	/**
	 * Getter pour la ville du client
	 * @return string
	 */
	public function getVille() {
		return $this->ville;
	}

	/**
	 * Setter pour la ville du client
	 * @var string
	 */
	public function setVille($ville) {
		$this->ville = $ville;
	}

	/**
	 * Getter pour le pays du client
	 * @return string
	 */
	public function getPays() {
		return $this->pays;
	}

	/**
	 * Setter pour le pays du client
	 * @var string
	 */
	public function setPays($pays) {
		$this->pays = $pays;
	}

	/**
	 * Getter pour le numéro de téléphone du client
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * Setter pour le numéro de téléphone du client
	 * @var string
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/**
	 * Getter pour le numéro de téléphone portable du client
	 * @return string
	 */
	public function getTelephonePortable() {
		return $this->telephonePortable;
	}

	/**
	 * Setter pour le numéro de téléphone portable du client
	 * @var string
	 */
	public function setTelephonePortable($telephonePortable) {
		$this->telephonePortable = $telephonePortable;
	}

	/**
	 * Getter pour l'email du client
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Setter pour l'email du client
	 * @var string
	 */
	public function setEmail($email) {
		$this->email = $email;
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