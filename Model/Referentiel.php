<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class Referentiel {
	/**
	 * Identifiant du r�f�rentiel
	 * @var integer
	 */
	private $id;

	/**
	 * Code du r�f�rentiel
	 * @var string
	 */
	private $code;

	/**
	 * Valeur du r�f�rentiel
	 * @var string
	 */
	private $valeur;
	
	/**
	 * Ann�e de validit� du r�f�rentiel
	 * @var DateTime
	 */
	private $anneeValidite;

	/**
	 * Date de cr�ation du r�f�rentiel
	 * @var DateTime
	 */
	private $dateCreation;

	/**
	 * Date de modification du r�f�rentiel
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
	 * Getter pour le code du r�f�rentiel
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Setter pour la code du r�f�rentiel
	 * @var string
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * Getter pour la valeur du r�f�rentiel
	 * @return string
	 */
	public function getValeur() {
		return $this->valeur;
	}

	/**
	 * Setter pour la valeur du r�f�rentiel
	 * @var string
	 */
	public function setValeur($valeur) {
		$this->valeur = $valeur;
	}

	/**
	 * Getter pour l'ann�e de validit� du r�f�rentiel
	 * @return string
	 */
	public function getAnneeValidite() {
		return $this->anneeValidite;
	}

	/**
	 * Setter pour l'ann�e de validit� du r�f�rentiel
	 * @var string
	 */
	public function setAnneeValidite($anneeValidite) {
		$this->anneeValidite = $anneeValidite;
	}

	/**
	 * Getter pour la date de cr�ation du r�f�rentiel
	 * @return DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}

	/**
	 * Setter pour la date de cr�ation du r�f�rentiel
	 * @var DateTime
	 */
	public function setDateCreation(\DateTime $dateCreation) {
		$this->dateCreation = $dateCreation;
	}

	/**
	 * Getter pour la date de modification du r�f�rentiel
	 * @return DateTime
	 */
	public function getDateModification() {
		return $this->dateModification;
	}

	/**
	 * Setter pour la date de modification du r�f�rentiel
	 * @var DateTime
	 */
	public function setDateModification(\DateTime $dateModification) {
		$this->dateModification = $dateModification;
	}

}

?>