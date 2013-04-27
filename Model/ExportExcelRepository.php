<?php
include_once 'CommunModel.php';
include_once 'Constante.php';
include_once 'Reservation.php';
include_once 'ReferentielRepository.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class ExportExcelRepository {

	const NOM_TEMPLATE_FICHE_INSCRIPTION = 'Fiche Inscription Camping-1.docx';

	const ASCII_CARTE_ID = 'F0A4';
	const ASCII_AUTRE_ID = 'F0A5';
	const ASCII_ACOMPTE = 'F0A6';
	const ASCII_PAIEMENT_CHEQUE = 'F0A7';
	const ASCII_PAIEMENT_ESPECE = 'F0A8';
	const ASCII_PAIEMENT_CV = 'F0A9';
	const ASCII_CASE_PLEINE = 'F0A2';
	const ASCII_CASE_VIDE = 'F0A3';

	public function __construct() {
		//Construction des singleton
	}

	/**
	 * Export de la réservation sous excel ou word
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation Réservation à exporter
	 * @return string Renvoie l'adresse vers le document généré relative à ce fichier
	 */
	public function exporterReservation(Reservation $reservation) {
		$DS = DIRECTORY_SEPARATOR;
		$repertoireTemplate = dirname(dirname(__FILE__)) . $DS . 'IHM' . $DS . 'template';
		$repertoireTmp = dirname(__FILE__) . $DS . 'tmp';
		$repertoireDonnees = $repertoireTmp . $DS . 'word';
		$fichierDonnees = $repertoireDonnees . $DS . 'document.xml';
		$fichierDonneesZip = 'word/document.xml';
		$nomFichierExport = 'Fiche1.docx';
		$retour = '';

		//On vide le répertoire des fichiers excel
		if (is_dir($repertoireDonnees)) {
			CommunModel::supprimerRepertoire($repertoireDonnees);
		}
		if (file_exists($repertoireTmp . $DS . $nomFichierExport)) {
			unlink($repertoireTmp . $DS . $nomFichierExport);
		}

		//On copie le template de base en renommant le fichier
		copy($repertoireTemplate . $DS . self::NOM_TEMPLATE_FICHE_INSCRIPTION,
		$repertoireTmp . $DS . $nomFichierExport);

		//On ouvre le fichier XLSX comme un ZIP
		$zip = new ZipArchive;
		if ($zip->open($repertoireTmp . $DS . $nomFichierExport) === TRUE) {
			//On extrait le fichier contenant les données
			$zip->extractTo($repertoireTmp, array($fichierDonneesZip));

			//On modifie le fichier avec les données de la réservation
			if (file_exists($fichierDonnees)) {
				$contenu = file_get_contents($fichierDonnees);
				$contenu = $this->remplirExcelReservation($contenu, $reservation);
				//Enregistrement des modifications
				$handle2 = fopen($fichierDonnees,'w+') or die("Une erreur est survenue lors de la génération du fichier Excel.");
				fwrite($handle2, $contenu);
				fclose($handle2);
			}

			//On remplace le fichier du template par le fichier contenant les données
			$zip->deleteName($fichierDonneesZip);
			$zip->addFile($fichierDonnees, $fichierDonneesZip);

			//On ferme le fichier et on supprime les fichiers temporaires
			$zip->close();
			CommunModel::supprimerRepertoire($repertoireDonnees);

			$retour = 'tmp/' . $nomFichierExport;
		} else {
			$retour = false;
		}

		return $retour;
	}

	/**
	 * Remplace le contenu du template Excel par les données de la réservation
	 * @param string $contenu
	 * @param Reservation $reservation Réservation à exporter
	 * @return string Retourne le contenu avec les valeurs modifiées
	 */
	private function remplirExcelReservation($contenu, Reservation $reservation) {
		$autreId = true;
		$searchCarre = array(self::ASCII_CARTE_ID, self::ASCII_AUTRE_ID,
		self::ASCII_ACOMPTE, self::ASCII_PAIEMENT_CHEQUE,
		self::ASCII_PAIEMENT_ESPECE, self::ASCII_PAIEMENT_CV);
		$replaceCarre = array(self::ASCII_CASE_VIDE, self::ASCII_CASE_VIDE,
		self::ASCII_CASE_VIDE, self::ASCII_CASE_VIDE,
		self::ASCII_CASE_VIDE, self::ASCII_CASE_VIDE);
		$client = $reservation->getClient();
		$referentielRepo = new ReferentielRepository();

		//En tête
		/********/
		$numEmplacement = '';
		if ($reservation->getNumeroEmplacement()) {
			$numEmplacement = $reservation->getNumeroEmplacement();
		}
		$newContenu = str_replace('{{REF_CLIENT}}', $client->getReference(),
		str_replace('{{DATE_JOUR}}', date('d/m/Y'),
		str_replace('{{NUM_EMPLACEMENT}}', $numEmplacement, $contenu)));

		//Partie client
		/**************/
		$newContenu = str_replace('{{NOM}}', strtoupper($client->getNom()),
		str_replace('{{PRENOM}}', ucfirst($client->getPrenom()),
		str_replace('{{RUE}}', $client->getAdresse1(),
		str_replace('{{COMPLEMENT}}', $client->getAdresse2(),
		str_replace('{{CODE_POSTAL}}', $client->getCodePostal(),
		str_replace('{{VILLE}}', $client->getVille(),
		str_replace('{{PAYS}}', $client->getPays(),
		str_replace('{{TEL_MOBILE}}', $client->getTelephonePortable(),
		str_replace('{{EMAIL}}', $client->getEmail(), $newContenu)))))))));

		//Partie réservation
		/*******************/
		/**
		 * @TODO: rajouter champ nb véhicules supp et pret matériel
		 */
		$carteIdPres = $reservation->getPieceIdPresentee();
		$nombreAdultes = intval($reservation->getNombreAdultes());
		$nombreEnfants = intval($reservation->getNombreEnfants());
		$nombreAnimaux = intval($reservation->getNombreAnimaux());
		$nombrePetitesTentes = intval($reservation->getNombrePetitesTentes());
		$nombreVans = intval($reservation->getNombreVans());
		$nombreGrandesTentes = intval($reservation->getNombreGrandesTentes());
		$nombreCaravanes = intval($reservation->getNombreCaravanes());
		$nombreCampingCars = intval($reservation->getNombreCampingCars());
		$electricite = intval($reservation->getElectricite());
		$nombreVehiculesSupp = intval(0);
		$nombreVisiteurs = $reservation->getNombreNuitesVisiteur();
		$pretMateriel = intval(0);

		$prixAdulte = $referentielRepo->getPrixCampeurAdulte();
		$prixEnfant = $referentielRepo->getPrixCampeurEnfant();
		$prixAnimal = $referentielRepo->getPrixAnimal();
		$prixPetiteTenteVan = $referentielRepo->getPrixPetiteTenteVan();
		$prixGrandeTenteCaravane = $referentielRepo->getPrixGrandeTenteCaravane();
		$prixCampingCar = $referentielRepo->getPrixCampingCar();
		$prixElectricite = $referentielRepo->getPrixElectricite();
		$prixVehiculeSupp = $referentielRepo->getPrixVehiculeSupp();
		$prixVisiteur = $referentielRepo->getPrixVisiteur();

		if (strcmp('carteId', $carteIdPres) === 0) {
			//Première occurence du carré
			$replaceCarre[0] = self::ASCII_CASE_PLEINE;
		}
		elseif (strcmp('autre', $carteIdPres) === 0) {
			//Deuxième occurence du carré
			$replaceCarre[1] = self::ASCII_CASE_PLEINE;
		}

		$dateArrivee = $reservation->getDateArrivee();
		if (!is_null($dateArrivee)) {
			$newContenu = str_replace('{{DATE_ARRIVEE}}', $dateArrivee->format('d/m/Y'), $newContenu);
		}
		$dateDepart = $reservation->getDateDepart();
		if (!is_null($dateDepart)) {
			$newContenu = str_replace('{{DATE_DEPART}}', $dateDepart->format('d/m/Y'), $newContenu);
		}
		if ((!is_null($dateDepart)) and (!is_null($dateArrivee))) {
			$interval = $dateDepart->diff($dateArrivee);
			$newContenu = str_replace('{{NB_NUITS}}', $interval->format('%a'), $newContenu);
		}

		//Prix
		$newContenu = str_replace('{{PRIX_CAMPEUR_ADULTE}}', $prixAdulte,
		str_replace('{{PRIX_CAMPEUR_ENFANT}}', $prixEnfant,
		str_replace('{{PRIX_ANIMAL}}', $prixAnimal,
		str_replace('{{PRIX_PETITE_TENTE_VAN}}', $prixPetiteTenteVan,
		str_replace('{{PRIX_GRANDE_TENTE_CARAVANE}}', $prixGrandeTenteCaravane,
		str_replace('{{PRIX_CAMPING_CAR}}', $prixCampingCar,
		str_replace('{{PRIX_ELECTRICITE}}', $prixElectricite,
		str_replace('{{PRIX_VEHICULE_SUPP}}', $prixVehiculeSupp,
		str_replace('{{PRIX_VISITEUR}}', $prixVisiteur, $newContenu)))))))));

		//Nombre
		$newContenu = str_replace('{{NB_ADULTES}}', $nombreAdultes,
		str_replace('{{NB_ENFANTS}}', $nombreEnfants,
		str_replace('{{NB_ANIMAUX}}', $nombreAnimaux,
		str_replace('{{NB_PETITES_TENTES}}', $nombrePetitesTentes,
		str_replace('{{NB_VANS}}', $nombreVans,
		str_replace('{{NB_GRANDES_TENTES}}', $nombreGrandesTentes,
		str_replace('{{NB_CARAVANES}}', $nombreCaravanes,
		str_replace('{{NB_CAMPING_CAR}}', $nombreCampingCars,
		str_replace('{{ELECTRICITE}}', $electricite,
		str_replace('{{NB_VEHICULES_SUPP}}', $nombreVehiculesSupp,
		str_replace('{{NB_VISITEURS}}', $nombreVisiteurs, $newContenu)))))))))));

		//Sous total
		$sousTotalAdulte = $nombreAdultes * $prixAdulte;
		$sousTotalEnfant = $nombreEnfants * $prixEnfant;
		$sousTotalAnimal = $nombreAnimaux * $prixAnimal;
		$sousTotalPetiteTente = $nombrePetitesTentes * $prixPetiteTenteVan;
		$sousTotalVan = $nombreVans * $prixPetiteTenteVan;
		$sousTotalGrandeTente = $nombreGrandesTentes * $prixGrandeTenteCaravane;
		$sousTotalCaravane = $nombreCaravanes * $prixGrandeTenteCaravane;
		$sousTotalCampingCar = $nombreCampingCars * $prixCampingCar;
		$sousTotalElectricite = $electricite * $prixElectricite;
		$sousTotalVehiculeSupp = $nombreVehiculesSupp * $prixVehiculeSupp;
		$sousTotalVisiteur = $nombreVisiteurs * $prixVisiteur;
		$sousTotalNuitees = $sousTotalAdulte + $sousTotalEnfant + $sousTotalAnimal +
		$sousTotalPetiteTente + $sousTotalVan + $sousTotalGrandeTente +
		$sousTotalCaravane + $sousTotalCampingCar + $sousTotalElectricite +
		$sousTotalVehiculeSupp + $sousTotalVisiteur;
		/**
		 * @TODO: Total séjour = pret matériel?
		 */
		$totalSejour = $sousTotalNuitees;

		$newContenu = str_replace('{{SOUS_TOTAL_ADULTE}}', $sousTotalAdulte,
		str_replace('{{SOUS_TOTAL_ENFANT}}', $sousTotalEnfant,
		str_replace('{{SOUS_TOTAL_ANIMAL}}', $sousTotalAnimal,
		str_replace('{{SOUS_TOTAL_PETITE_TENTE}}', $sousTotalPetiteTente,
		str_replace('{{SOUS_TOTAL_VAN}}', $sousTotalVan,
		str_replace('{{SOUS_TOTAL_GRANDE_TENTE}}', $sousTotalGrandeTente,
		str_replace('{{SOUS_TOTAL_CARAVANE}}', $sousTotalCaravane,
		str_replace('{{SOUS_TOTAL_CAMPING_CAR}}', $sousTotalCampingCar,
		str_replace('{{SOUS_TOTAL_ELECTRICITE}}', $sousTotalElectricite,
		str_replace('{{SOUS_TOTAL_VEHICULE_SUPP}}', $sousTotalVehiculeSupp,
		str_replace('{{SOUS_TOTAL_VISITEUR}}', $sousTotalVisiteur,
		str_replace('{{SOUS_TOTAL_NUITEES}}', $sousTotalNuitees,
		str_replace('{{TOTAL_SEJOUR}}', $totalSejour, $newContenu)))))))))))));

		//Acompte
		$acompte = intval($reservation->getArrhes());
		if ($acompte !== 0) {
			//Troisième occurence du carré
			$replaceCarre[2] = self::ASCII_CASE_PLEINE;
		}
		$newContenu = str_replace('{{MONTANT_ACOMPTE}}', $acompte, $newContenu);

		//Remplacement des carrés
		$newContenu = str_replace($searchCarre, $replaceCarre, $newContenu);

		return $newContenu;
	}
}

?>