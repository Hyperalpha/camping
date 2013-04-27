<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class Referentiel {
	/**
	 * Identifiant du référentiel
	 * @var integer
	 */
	private $id;

	/**
	 * Code du référentiel
	 * @var string
	 */
	private $code;

	/**
	 * Valeur du référentiel
	 * @var string
	 */
	private $valeur;
	
	/**
	 * Année de validité du référentiel
	 * @var DateTime
	 */
	private $anneeValidite;

	/**
	 * Date de création du référentiel
	 * @var DateTime
	 */
	private $dateCreation;

	/**
	 * Date de modification du référentiel
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
	 * Getter pour le code du référentiel
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Setter pour la code du référentiel
	 * @var string
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * Getter pour la valeur du référentiel
	 * @return string
	 */
	public function getValeur() {
		return $this->valeur;
	}

	/**
	 * Setter pour la valeur du référentiel
	 * @var string
	 */
	public function setValeur($valeur) {
		$this->valeur = $valeur;
	}

	/**
	 * Getter pour l'année de validité du référentiel
	 * @return string
	 */
	public function getAnneeValidite() {
		return $this->anneeValidite;
	}

	/**
	 * Setter pour l'année de validité du référentiel
	 * @var string
	 */
	public function setAnneeValidite($anneeValidite) {
		$this->anneeValidite = $anneeValidite;
	}

	/**
	 * Getter pour la date de création du référentiel
	 * @return DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}

	/**
	 * Setter pour la date de création du référentiel
	 * @var DateTime
	 */
	public function setDateCreation(\DateTime $dateCreation) {
		$this->dateCreation = $dateCreation;
	}

	/**
	 * Getter pour la date de modification du référentiel
	 * @return DateTime
	 */
	public function getDateModification() {
		return $this->dateModification;
	}

	/**
	 * Setter pour la date de modification du référentiel
	 * @var DateTime
	 */
	public function setDateModification(\DateTime $dateModification) {
		$this->dateModification = $dateModification;
	}

}

?>