<?php
include_once 'CommunModel.php';
include_once 'Constante.php';
include_once 'Reservation.php';
include_once 'Facture.php';
include_once 'ReferentielRepository.php';
include_once 'FactureRepository.php';
include_once 'ReservationRepository.php';

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class ExportExcelRepository {

	const NOM_TEMPLATE_FICHE_INSCRIPTION_CAMPING = 'Fiche Inscription Camping-2.docx';
	const NOM_TEMPLATE_FICHE_INSCRIPTION_ROULOTTES = 'Fiche Inscription Roulotte-2.docx';
	const NOM_TEMPLATE_FACTURE_CAMPING = 'Formulaire Facture Camping-2.docx';
	const NOM_TEMPLATE_FACTURE_ROULOTTES = 'Formulaire Facture Roulotte-1.docx';

	const ASCII_CARTE_ID = 'F0A4';
	const ASCII_AUTRE_ID = 'F0A5';
	const ASCII_ACOMPTE = 'F0A6';
	const ASCII_PAIEMENT_CHEQUE = 'F0A7';
	const ASCII_PAIEMENT_ESPECE = 'F0A8';
	const ASCII_PAIEMENT_CV = 'F0A9';
	const ASCII_CASE_PLEINE = 'F0A2';
	const ASCII_CASE_VIDE = 'F0A3';

	const DEVISE = "€";
	const DEVISE_WORD = "â‚¬";
	const SEPARATEUR_PRIX_FACTURE = " x ";

	const LIBELLE_REMISE_EXCEPTIONNELLE = "Remise exceptionnelle";

	private $referentielRepo;
	private $factureRepository;
	private $reservationRepository;

	public function __construct() {
		//Construction des singleton
		$this->referentielRepo = new ReferentielRepository();
		$this->factureRepository = new FactureRepository();
		$this->reservationRepository = new ReservationRepository();
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

		//Le fichier exporté portera le nom du client et la date du jour
		$client = $reservation->getClient();
		if (!is_null($client)) {
			$nomExport = preg_replace("#[^a-zA-Z \-0-9]#", "", utf8_decode($client->getNom()
					. ' ' . $client->getPrenom() . ' ' . date('d-m-Y')));
			$nomFichierExport = $nomExport . '.docx';
		}

		//On vide le répertoire des fichiers excel
		if (is_dir($repertoireTmp)) {
			CommunModel::supprimerContenuRepertoire($repertoireTmp);
		}
		if (file_exists($repertoireTmp . $DS . $nomFichierExport)) {
			unlink($repertoireTmp . $DS . $nomFichierExport);
		}

		//On copie le template de base en renommant le fichier
		if ($reservation->getRoulotteRouge() or $reservation->getRoulotteBleue() 
				or $reservation->getTenteSafari()) {
			//Fiche roulottes
			copy($repertoireTemplate . $DS . self::NOM_TEMPLATE_FICHE_INSCRIPTION_ROULOTTES,
			$repertoireTmp . $DS . $nomFichierExport);
		}
		else {
			//Fiche camping
			copy($repertoireTemplate . $DS . self::NOM_TEMPLATE_FICHE_INSCRIPTION_CAMPING,
			$repertoireTmp . $DS . $nomFichierExport);
		}

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
	 * Export de la facture d'une réservation sous excel ou word
	 * @author Arnaud DUPUIS
	 * @param Reservation $reservation Réservation à exporter
	 * @param boolean $regenererFacture Doit-on regénèrer la facture ou afficher l'ancienne?
	 * @return string Renvoie l'adresse vers le document généré relative à ce fichier
	 */
	public function exporterFactureReservation(Reservation $reservation, $regenererFacture) {
		$DS = DIRECTORY_SEPARATOR;
		$repertoireTemplate = dirname(dirname(__FILE__)) . $DS . 'IHM' . $DS . 'template';
		$repertoireTmp = dirname(__FILE__) . $DS . 'tmp';
		$repertoireDonnees = $repertoireTmp . $DS . 'word';
		$fichierDonnees = $repertoireDonnees . $DS . 'document.xml';
		$fichierDonneesZip = 'word/document.xml';
		$nomFichierExport = 'Facture1.docx';
		$retour = '';

		//Le fichier exporté portera la référence de la réservation, du client ainsi que la date
		$client = $reservation->getClient();
		if (!is_null($client)) {
			//On recherche la facture
			$facture = $this->factureRepository
			->rechercherFacture($reservation->getReference());
			if (!is_null($facture)) {
				$facture = $facture[0];
			}

			//Si la facture n'existe pas, on la crée
			if (is_null($facture) or $regenererFacture == true) {
				if (is_null($facture)) {
					$facture = new Facture();
				}
				$dateArrivee = $reservation->getDateArrivee();
				$dateDepart = $reservation->getDateDepart();
				$interval = $dateDepart->diff($dateArrivee);
				$nbNuitees = intval($interval->format('%a'));
				$dateDebutPeriodeHauteRoulotte = $this->referentielRepo->getDateDebutPeriodeHauteRoulotte();
				$dateFinPeriodeHauteRoulotte = $this->referentielRepo->getDateFinPeriodeHauteRoulotte();

				//Roulotte rouge
				if ($reservation->getRoulotteRouge()) {
					$nbNuitsRoulotteRouge = PlanningCampingController::getNbJoursHautBasRoulottes(
							$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
					$nombreRoulotteRougePeriodeBasse = round((intval($nbNuitsRoulotteRouge->nbJoursBas) / 7) * 100) / 100;
					$nombreRoulotteRougePeriodeHaute = round((intval($nbNuitsRoulotteRouge->nbJoursHaut) / 7) * 100) / 100;
				}
				else {
					$nombreRoulotteRougePeriodeBasse = 0;
					$nombreRoulotteRougePeriodeHaute = 0;
				}
				//Roulette bleue
				if ($reservation->getRoulotteBleue()) {
					$nbNuitsRoulotteBleue = PlanningCampingController::getNbJoursHautBasRoulottes(
							$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
					$nombreRoulotteBleuePeriodeBasse = round((intval($nbNuitsRoulotteBleue->nbJoursBas) / 7) * 100) / 100;
					$nombreRoulotteBleuePeriodeHaute = round((intval($nbNuitsRoulotteBleue->nbJoursHaut) / 7) * 100) / 100;
				}
				else {
					$nombreRoulotteBleuePeriodeBasse = 0;
					$nombreRoulotteBleuePeriodeHaute = 0;
				}
				//Tente safari
				if ($reservation->getTenteSafari()) {
					$nbNuitsTenteSafari = PlanningCampingController::getNbJoursHautBasRoulottes(
							$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
					$nombreTenteSafariPeriodeBasse = round((intval($nbNuitsTenteSafari->nbJoursBas) / 7) * 100) / 100;
					$nombreTenteSafariPeriodeHaute = round((intval($nbNuitsTenteSafari->nbJoursHaut) / 7) * 100) / 100;
				}
				else {
					$nombreTenteSafariPeriodeBasse = 0;
					$nombreTenteSafariPeriodeHaute = 0;
				}

				$facture->setId('F' .  date('YmdHis') . '_' . $reservation->getReference() . '-'
						. $client->getReference());
				$facture->setReferenceReservation($reservation->getReference());
				$facture->setDateGeneration(new \DateTime());
				$facture->setDevise(self::DEVISE);
				$facture->setCampeurAdulte(intval($reservation->getNombreAdultes())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixCampeurAdulte());
				$facture->setCampeurEnfant(intval($reservation->getNombreEnfants())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixCampeurEnfant());
				$facture->setAnimal(intval($reservation->getNombreAnimaux())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixAnimal());
				$facture->setTarif1(intval($reservation->getNombreTarif1())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixTarif1());
				$facture->setTarif2(intval($reservation->getNombreTarif2())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixTarif2());
				$facture->setTarif3(intval($reservation->getNombreTarif3())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixTarif3());
				$facture->setElectricite(intval($reservation->getElectricite())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixElectricite());
				$facture->setVehiculeSupplementaire(intval($reservation->getNombreVehiculesSupplementaires())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixVehiculeSupp());
				$facture->setNombreVisiteurs(intval($reservation->getNombreNuitesVisiteur())
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixVisiteur());
				$facture->setRoulotteRougePeriodeBasse($nombreRoulotteRougePeriodeBasse
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixRoulotteRougePeriodeBasse());
				$facture->setRoulotteRougePeriodeHaute($nombreRoulotteRougePeriodeHaute
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixRoulotteRougePeriodeHaute());
				$facture->setRoulotteBleuePeriodeBasse($nombreRoulotteBleuePeriodeBasse
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixRoulotteBleuePeriodeBasse());
				$facture->setRoulotteBleuePeriodeHaute($nombreRoulotteBleuePeriodeHaute
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixRoulotteBleuePeriodeHaute());
				$facture->setTenteSafariPeriodeBasse($nombreTenteSafariPeriodeBasse
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixTenteSafariPeriodeBasse());
				$facture->setTenteSafariPeriodeHaute($nombreTenteSafariPeriodeHaute
						. self::SEPARATEUR_PRIX_FACTURE . $this->referentielRepo->getPrixTenteSafariPeriodeHaute());
				$facture->setRemiseExceptionnelle($reservation->getRemiseExceptionnelle());

				//On relie la facture à la réservation
				$reservation->setFacture($facture);
				$this->reservationRepository->enregistrerReservation($reservation);
			}

			$nomFichierExport = preg_replace("#[^a-zA-Z \-_0-9]#", "",
					utf8_decode('Facture ' .  $facture->getId())). '.docx';
		}

		//On vide le répertoire des fichiers excel
		if (is_dir($repertoireTmp)) {
			CommunModel::supprimerContenuRepertoire($repertoireTmp);
		}
		if (file_exists($repertoireTmp . $DS . $nomFichierExport)) {
			unlink($repertoireTmp . $DS . $nomFichierExport);
		}

		//On copie le template de base en renommant le fichier
		if ($reservation->getRoulotteRouge() or $reservation->getRoulotteBleue() 
				or $reservation->getTenteSafari()) {
			//Facture roulottes
			copy($repertoireTemplate . $DS . self::NOM_TEMPLATE_FACTURE_ROULOTTES,
			$repertoireTmp . $DS . $nomFichierExport);
		}
		else {
			//Facture camping
			copy($repertoireTemplate . $DS . self::NOM_TEMPLATE_FACTURE_CAMPING,
			$repertoireTmp . $DS . $nomFichierExport);
		}

		//On ouvre le fichier XLSX comme un ZIP
		$zip = new ZipArchive;
		if ($zip->open($repertoireTmp . $DS . $nomFichierExport) === TRUE) {
			//On extrait le fichier contenant les données
			$zip->extractTo($repertoireTmp, array($fichierDonneesZip));

			//On modifie le fichier avec les données de la réservation
			if (file_exists($fichierDonnees)) {
				$contenu = file_get_contents($fichierDonnees);
				$contenu = $this->remplirExcelFacture($contenu, $reservation);
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

		$dateArrivee = $reservation->getDateArrivee();
		$dateDepart = $reservation->getDateDepart();
		$interval = $dateDepart->diff($dateArrivee);
		$nbNuitees = intval($interval->format('%a'));

		$dateDebutPeriodeHauteRoulotte = $this->referentielRepo->getDateDebutPeriodeHauteRoulotte();
		$dateFinPeriodeHauteRoulotte = $this->referentielRepo->getDateFinPeriodeHauteRoulotte();

		//En tête
		/********/
		$numEmplacement = '';
		if ($reservation->getNumeroEmplacement()) {
			$numEmplacement = $reservation->getNumeroEmplacement();
		}
		$newContenu = str_replace('{{REF_CLIENT}}', $client->getReference(),
			str_replace('{{REF_RESERVATION}}', $reservation->getReference(),
			str_replace('{{DATE_JOUR}}', date('d/m/Y'),
			str_replace('{{NUM_EMPLACEMENT}}', $numEmplacement, $contenu))));

		//Partie client
		/**************/
		$newContenu = str_replace('{{NOM}}', strtoupper($client->getNom()),
			str_replace('{{PRENOM}}', ucfirst($client->getPrenom()),
			str_replace('{{RUE}}', $client->getAdresse1(),
			str_replace('{{CODE_POSTAL}}', $client->getCodePostal(),
			str_replace('{{VILLE}}', $client->getVille(),
			str_replace('{{PAYS}}', $client->getPays(),
			str_replace('{{TEL_MOBILE}}', $client->getTelephonePortable(),
			str_replace('{{EMAIL}}', $client->getEmail(), $newContenu))))))));

		//Partie réservation
		/*******************/
		$carteIdPres = $reservation->getPieceIdPresentee();
		$nombreAdultes = intval($reservation->getNombreAdultes());
		$nombreEnfants = intval($reservation->getNombreEnfants());
		$nombreAnimaux = intval($reservation->getNombreAnimaux());
		$nombreTarif1 = intval($reservation->getNombreTarif1());
		$nombreTarif2 = intval($reservation->getNombreTarif2());
		$nombreTarif3 = intval($reservation->getNombreTarif3());
		$electricite = intval($reservation->getElectricite());
		$nombreVehiculesSupp = intval($reservation->getNombreVehiculesSupplementaires());
		$nombreVisiteurs = intval($reservation->getNombreNuitesVisiteur());
		if ($reservation->getRoulotteRouge()) {
			$nbNuitsRoulotteRouge = PlanningCampingController::getNbJoursHautBasRoulottes(
					$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
			$nombreRoulotteRougePeriodeBasse = $nbNuitsRoulotteRouge->nbJoursBas;
			$nombreRoulotteRougePeriodeHaute = $nbNuitsRoulotteRouge->nbJoursHaut;
		}
		else {
			$nombreRoulotteRougePeriodeBasse = 0;
			$nombreRoulotteRougePeriodeHaute = 0;
		}
		if ($reservation->getRoulotteBleue()) {
			$nbNuitsRoulotteBleue = PlanningCampingController::getNbJoursHautBasRoulottes(
					$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
			$nombreRoulotteBleuePeriodeBasse = $nbNuitsRoulotteBleue->nbJoursBas;
			$nombreRoulotteBleuePeriodeHaute = $nbNuitsRoulotteBleue->nbJoursHaut;
		}
		else {
			$nombreRoulotteBleuePeriodeBasse = 0;
			$nombreRoulotteBleuePeriodeHaute = 0;
		}
		if ($reservation->getTenteSafari()) {
			$nbNuitsTenteSafari = PlanningCampingController::getNbJoursHautBasRoulottes(
					$dateArrivee, $dateDepart, $dateDebutPeriodeHauteRoulotte, $dateFinPeriodeHauteRoulotte);
			$nombreTenteSafariPeriodeBasse = $nbNuitsTenteSafari->nbJoursBas;
			$nombreTenteSafariPeriodeHaute = $nbNuitsTenteSafari->nbJoursHaut;
		}
		else {
			$nombreTenteSafariPeriodeBasse = 0;
			$nombreTenteSafariPeriodeHaute = 0;
		}

		$prixAdulte = $this->referentielRepo->getPrixCampeurAdulte();
		$prixEnfant = $this->referentielRepo->getPrixCampeurEnfant();
		$prixAnimal = $this->referentielRepo->getPrixAnimal();
		$prixTarif1 = $this->referentielRepo->getPrixTarif1();
		$prixTarif2 = $this->referentielRepo->getPrixTarif2();
		$prixTarif3 = $this->referentielRepo->getPrixTarif3();
		$prixElectricite = $this->referentielRepo->getPrixElectricite();
		$prixVehiculeSupp = $this->referentielRepo->getPrixVehiculeSupp();
		$prixVisiteur = $this->referentielRepo->getPrixVisiteur();
		$prixRoulotteRougePeriodeBasse = $this->referentielRepo->getPrixRoulotteRougePeriodeBasse();
		$prixRoulotteRougePeriodeHaute = $this->referentielRepo->getPrixRoulotteRougePeriodeHaute();
		$prixRoulotteBleuePeriodeBasse = $this->referentielRepo->getPrixRoulotteBleuePeriodeBasse();
		$prixRoulotteBleuePeriodeHaute = $this->referentielRepo->getPrixRoulotteBleuePeriodeHaute();
		$prixTenteSafariPeriodeBasse = $this->referentielRepo->getPrixTenteSafariPeriodeBasse();
		$prixTenteSafariPeriodeHaute = $this->referentielRepo->getPrixTenteSafariPeriodeHaute();

		if (strcmp('carteId', $carteIdPres) === 0) {
			//Première occurence du carré
			$replaceCarre[0] = self::ASCII_CASE_PLEINE;
		}
		elseif (strcmp('autre', $carteIdPres) === 0) {
			//Deuxième occurence du carré
			$replaceCarre[1] = self::ASCII_CASE_PLEINE;
		}

		if (!is_null($dateArrivee)) {
			$newContenu = str_replace('{{DATE_ARRIVEE}}', $dateArrivee->format('d/m/Y'), $newContenu);
		}
		if (!is_null($dateDepart)) {
			$newContenu = str_replace('{{DATE_DEPART}}', $dateDepart->format('d/m/Y'), $newContenu);
		}
		if ((!is_null($dateDepart)) and (!is_null($dateArrivee))) {
			$newContenu = str_replace('{{NB_NUITS}}', $nbNuitees, $newContenu);
		}

		//Prix
		$newContenu = str_replace('{{PRIX_CAMPEUR_ADULTE}}', $prixAdulte,
			str_replace('{{PRIX_CAMPEUR_ENFANT}}', $prixEnfant,
			str_replace('{{PRIX_ANIMAL}}', $prixAnimal,
			str_replace('{{PRIX_TARIF1}}', $prixTarif1,
			str_replace('{{PRIX_TARIF2}}', $prixTarif2,
			str_replace('{{PRIX_TARIF3}}', $prixTarif3,
			str_replace('{{PRIX_ELECTRICITE}}', $prixElectricite,
			str_replace('{{PRIX_VEHICULE_SUPP}}', $prixVehiculeSupp,
			str_replace('{{PRIX_VISITEUR}}', $prixVisiteur,
			str_replace('{{PRIX_ROULOTTE_ROUGE_BASSE}}', $prixRoulotteRougePeriodeBasse,
			str_replace('{{PRIX_ROULOTTE_ROUGE_HAUTE}}', $prixRoulotteRougePeriodeHaute,
			str_replace('{{PRIX_ROULOTTE_BLEUE_BASSE}}', $prixRoulotteBleuePeriodeBasse,
			str_replace('{{PRIX_ROULOTTE_BLEUE_HAUTE}}', $prixRoulotteBleuePeriodeHaute,
			str_replace('{{PRIX_TENTE_SAFARI_BASSE}}', $prixTenteSafariPeriodeBasse,
			str_replace('{{PRIX_TENTE_SAFARI_HAUTE}}', $prixTenteSafariPeriodeHaute,
			$newContenu)))))))))))))));

		//Nombre
		$newContenu = str_replace('{{NB_ADULTES}}', $nombreAdultes,
			str_replace('{{NB_ENFANTS}}', $nombreEnfants,
			str_replace('{{NB_ANIMAUX}}', $nombreAnimaux,
			str_replace('{{NB_TARIF1}}', $nombreTarif1,
			str_replace('{{NB_TARIF2}}', $nombreTarif2,
			str_replace('{{NB_TARIF3}}', $nombreTarif3,
			str_replace('{{ELECTRICITE}}', $electricite,
			str_replace('{{NB_VEHICULES_SUPP}}', $nombreVehiculesSupp,
			str_replace('{{NB_VISITEURS}}', $nombreVisiteurs, $newContenu)))))))));

		//Sous total
		$sousTotalAdulte = $nombreAdultes * $prixAdulte;
		$sousTotalEnfant = $nombreEnfants * $prixEnfant;
		$sousTotalAnimal = $nombreAnimaux * $prixAnimal;
		$sousTotalTarif1 = $nombreTarif1 * $prixTarif1;
		$sousTotalTarif2 = $nombreTarif2 * $prixTarif2;
		$sousTotalTarif3 = $nombreTarif3 * $prixTarif3;
		$sousTotalElectricite = $electricite * $prixElectricite;
		$sousTotalVehiculeSupp = $nombreVehiculesSupp * $prixVehiculeSupp;
		$sousTotalVisiteur = $nombreVisiteurs * $prixVisiteur;

		$sousTotalRoulotteRougePeriodeBasse = round($nombreRoulotteRougePeriodeBasse
				* ($prixRoulotteRougePeriodeBasse / 7) * 100) / 100;
		$sousTotalRoulotteRougePeriodeHaute = round($nombreRoulotteRougePeriodeHaute
				* ($prixRoulotteRougePeriodeHaute / 7) * 100) / 100;
		$sousTotalRoulotteBleuePeriodeBasse = round($nombreRoulotteBleuePeriodeBasse
				* ($prixRoulotteBleuePeriodeBasse / 7) * 100) / 100;
		$sousTotalRoulotteBleuePeriodeHaute = round($nombreRoulotteBleuePeriodeHaute
				* ($prixRoulotteBleuePeriodeHaute / 7) * 100) / 100;
		$sousTotalTenteSafariPeriodeBasse = round($nombreTenteSafariPeriodeBasse
				* ($prixTenteSafariPeriodeBasse / 7) * 100) / 100;
		$sousTotalTenteSafariPeriodeHaute = round($nombreTenteSafariPeriodeHaute
				* ($prixTenteSafariPeriodeHaute / 7) * 100) / 100;

		$sousTotalNuitees = $sousTotalAdulte + $sousTotalEnfant + $sousTotalAnimal +
		$sousTotalTarif1 + $sousTotalTarif2 +
		$sousTotalTarif3 + $sousTotalElectricite +
		$sousTotalVehiculeSupp + $sousTotalVisiteur;
		$totalSejourRoulottes = $sousTotalRoulotteRougePeriodeBasse +
		$sousTotalRoulotteRougePeriodeHaute + $sousTotalRoulotteBleuePeriodeBasse +
		$sousTotalRoulotteBleuePeriodeHaute + $sousTotalTenteSafariPeriodeBasse +
		$sousTotalTenteSafariPeriodeHaute;
		$totalSejour = $sousTotalNuitees * $nbNuitees;

		$newContenu = str_replace('{{SOUS_TOTAL_ADULTE}}', $sousTotalAdulte,
			str_replace('{{SOUS_TOTAL_ENFANT}}', $sousTotalEnfant,
			str_replace('{{SOUS_TOTAL_ANIMAL}}', $sousTotalAnimal,
			str_replace('{{SOUS_TOTAL_TARIF1}}', $sousTotalTarif1,
			str_replace('{{SOUS_TOTAL_TARIF2}}', $sousTotalTarif2,
			str_replace('{{SOUS_TOTAL_TARIF3}}', $sousTotalTarif3,
			str_replace('{{SOUS_TOTAL_ELECTRICITE}}', $sousTotalElectricite,
			str_replace('{{SOUS_TOTAL_VEHICULE_SUPP}}', $sousTotalVehiculeSupp,
			str_replace('{{SOUS_TOTAL_VISITEUR}}', $sousTotalVisiteur,
			str_replace('{{SOUS_TOTAL_ROULOTTE_ROUGE_BASSE}}', $sousTotalRoulotteRougePeriodeBasse,
			str_replace('{{SOUS_TOTAL_ROULOTTE_ROUGE_HAUTE}}', $sousTotalRoulotteRougePeriodeHaute,
			str_replace('{{SOUS_TOTAL_ROULOTTE_BLEUE_BASSE}}', $sousTotalRoulotteBleuePeriodeBasse,
			str_replace('{{SOUS_TOTAL_ROULOTTE_BLEUE_HAUTE}}', $sousTotalRoulotteBleuePeriodeHaute,
			str_replace('{{SOUS_TOTAL_TENTE_SAFARI_BASSE}}', $sousTotalTenteSafariPeriodeBasse,
			str_replace('{{SOUS_TOTAL_TENTE_SAFARI_HAUTE}}', $sousTotalTenteSafariPeriodeHaute,
			str_replace('{{SOUS_TOTAL_NUITEES}}', $sousTotalNuitees,
			str_replace('{{TOTAL_SEJOUR_ROULOTTES}}', $totalSejourRoulottes,
			str_replace('{{TOTAL_SEJOUR}}', $totalSejour, $newContenu))))))))))))))))));

		//Acompte
		$acompte = floatval($reservation->getArrhes());
		if ($acompte != 0) {
			//Troisième occurence du carré
			$replaceCarre[2] = self::ASCII_CASE_PLEINE;
		}
		$newContenu = str_replace('{{MONTANT_ACOMPTE}}', $acompte, $newContenu);

		//Remplacement des carrés
		$newContenu = str_replace($searchCarre, $replaceCarre, $newContenu);

		return $newContenu;
	}

	/**
	 * Remplace le contenu du template Excel par les données de la facture d'une réservation
	 * @param string $contenu
	 * @param Reservation $reservation Réservation de la facture à exporter
	 * @return string Retourne le contenu avec les valeurs modifiées
	 */
	private function remplirExcelFacture($contenu, Reservation $reservation) {
		$client = $reservation->getClient();
		$facture = $reservation->getFacture();

		$nombreAdultes = null;
		$nombreEnfants = null;
		$nombreAnimaux = null;
		$nombreTarif1 = null;
		$nombreTarif2 = null;
		$nombreTarif3 = null;
		$electricite = null;
		$nombreVehiculesSupp = null;
		$nombreVisiteurs = null;
		$nombreRoulotteRougeBas = null;
		$nombreRoulotteRougeHaut = null;
		$nombreRoulotteBleueBas = null;
		$nombreRoulotteBleueHaut = null;
		$nombreTenteSafariBas = null;
		$nombreTenteSafariHaut = null;
		$prixAdulte = null;
		$prixEnfant = null;
		$prixAnimal = null;
		$prixTarif1 = null;
		$prixTarif2 = null;
		$prixTarif3 = null;
		$prixElectricite = null;
		$prixVehiculeSupp = null;
		$prixVisiteur = null;
		$prixRoulotteRougeBas = null;
		$prixRoulotteRougeHaut = null;
		$prixRoulotteBleueBas = null;
		$prixRoulotteBleueHaut = null;
		$prixTenteSafariBas = null;
		$prixTenteSafariHaut = null;
		$dateArrivee = $reservation->getDateArrivee();
		$dateDepart = $reservation->getDateDepart();
		$interval = $dateDepart->diff($dateArrivee);
		$nbNuitees = intval($interval->format('%a'));

		//En tête
		/********/
		$newContenu = str_replace('{{DATE_FACTURE}}', $facture->getDateGeneration()->format('d/m/Y'),
			str_replace('{{NOM}}', strtoupper($client->getNom()),
			str_replace('{{PRENOM}}', ucfirst($client->getPrenom()), $contenu)));

		//Partie réservation
		/*******************/
		//Décomposition des données de la facture
		if (!is_null($facture)) {
			$tabCampeurAdulte = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getCampeurAdulte());
			$tabCampeurEnfant = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getCampeurEnfant());
			$tabAnimal = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getAnimal());
			$tabTarif1 = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getTarif1());
			$tabTarif2 = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getTarif2());
			$tabTarif3 = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getTarif3());
			$tabElectricite = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getElectricite());
			$tabVehiculeSupplementaire = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getVehiculeSupplementaire());
			$tabNombreVisiteurs = explode(self::SEPARATEUR_PRIX_FACTURE, $facture->getNombreVisiteurs());
			$tabRoulotteRougePeriodeBasse = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getRoulotteRougePeriodeBasse());
			$tabRoulotteRougePeriodeHaute = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getRoulotteRougePeriodeHaute());
			$tabRoulotteBleuePeriodeBasse = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getRoulotteBleuePeriodeBasse());
			$tabRoulotteBleuePeriodeHaute = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getRoulotteBleuePeriodeHaute());
			$tabTenteSafariPeriodeBasse = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getTenteSafariPeriodeBasse());
			$tabTenteSafariPeriodeHaute = explode(self::SEPARATEUR_PRIX_FACTURE,
					$facture->getTenteSafariPeriodeHaute());

			if (count($tabCampeurAdulte) == 2) {
				$nombreAdultes = intval($tabCampeurAdulte[0]);
				$prixAdulte = floatval($tabCampeurAdulte[1]);
			}
			if (count($tabCampeurEnfant) == 2) {
				$nombreEnfants = intval($tabCampeurEnfant[0]);
				$prixEnfant = floatval($tabCampeurEnfant[1]);
			}
			if (count($tabAnimal) == 2) {
				$nombreAnimaux = intval($tabAnimal[0]);
				$prixAnimal = floatval($tabAnimal[1]);
			}
			if (count($tabTarif1) == 2) {
				$nombreTarif1 = intval($tabTarif1[0]);
				$prixTarif1 = floatval($tabTarif1[1]);
			}
			if (count($tabTarif2) == 2) {
				$nombreTarif2 = intval($tabTarif2[0]);
				$prixTarif2 = floatval($tabTarif2[1]);
			}
			if (count($tabTarif3) == 2) {
				$nombreTarif3 = intval($tabTarif3[0]);
				$prixTarif3 = floatval($tabTarif3[1]);
			}
			if (count($tabElectricite) == 2) {
				$electricite = intval($tabElectricite[0]);
				$prixElectricite = floatval($tabElectricite[1]);
			}
			if (count($tabVehiculeSupplementaire) == 2) {
				$nombreVehiculesSupp = intval($tabVehiculeSupplementaire[0]);
				$prixVehiculeSupp = floatval($tabVehiculeSupplementaire[1]);
			}
			if (count($tabNombreVisiteurs) == 2) {
				$nombreVisiteurs = intval($tabNombreVisiteurs[0]);
				$prixVisiteur = floatval($tabNombreVisiteurs[1]);
			}
			if (count($tabRoulotteRougePeriodeBasse) == 2) {
				$nombreRoulotteRougeBas = floatval($tabRoulotteRougePeriodeBasse[0]);
				$prixRoulotteRougeBas = floatval($tabRoulotteRougePeriodeBasse[1]);
			}
			if (count($tabRoulotteRougePeriodeHaute) == 2) {
				$nombreRoulotteRougeHaut = floatval($tabRoulotteRougePeriodeHaute[0]);
				$prixRoulotteRougeHaut = floatval($tabRoulotteRougePeriodeHaute[1]);
			}
			if (count($tabRoulotteBleuePeriodeBasse) == 2) {
				$nombreRoulotteBleueBas = floatval($tabRoulotteBleuePeriodeBasse[0]);
				$prixRoulotteBleueBas = floatval($tabRoulotteBleuePeriodeBasse[1]);
			}
			if (count($tabRoulotteBleuePeriodeHaute) == 2) {
				$nombreRoulotteBleueHaut = floatval($tabRoulotteBleuePeriodeHaute[0]);
				$prixRoulotteBleueHaut = floatval($tabRoulotteBleuePeriodeHaute[1]);
			}
			if (count($tabTenteSafariPeriodeBasse) == 2) {
				$nombreTenteSafariBas = floatval($tabTenteSafariPeriodeBasse[0]);
				$prixTenteSafariBas = floatval($tabTenteSafariPeriodeBasse[1]);
			}
			if (count($tabTenteSafariPeriodeHaute) == 2) {
				$nombreTenteSafariHaut = floatval($tabTenteSafariPeriodeHaute[0]);
				$prixTenteSafariHaut = floatval($tabTenteSafariPeriodeHaute[1]);
			}
		}

		if (!is_null($dateArrivee)) {
			$newContenu = str_replace('{{DATE_ARRIVEE}}', $dateArrivee->format('d/m/Y'), $newContenu);
		}
		if (!is_null($dateDepart)) {
			$newContenu = str_replace('{{DATE_DEPART}}', $dateDepart->format('d/m/Y'), $newContenu);
		}
		if ((!is_null($dateDepart)) and (!is_null($dateArrivee))) {
			$newContenu = str_replace('{{NB_NUITS}}', $nbNuitees, $newContenu);
		}

		//Prix
		$newContenu = str_replace('{{PRIX_CAMPEUR_ADULTE}}', $prixAdulte,
			str_replace('{{PRIX_CAMPEUR_ENFANT}}', $prixEnfant,
			str_replace('{{PRIX_ANIMAL}}', $prixAnimal,
			str_replace('{{PRIX_TARIF1}}', $prixTarif1,
			str_replace('{{PRIX_TARIF2}}', $prixTarif2,
			str_replace('{{PRIX_TARIF3}}', $prixTarif3,
			str_replace('{{PRIX_ELECTRICITE}}', $prixElectricite,
			str_replace('{{PRIX_VEHICULE_SUPP}}', $prixVehiculeSupp,
			str_replace('{{PRIX_VISITEUR}}', $prixVisiteur,
			str_replace('{{PRIX_ROULOTTE_ROUGE_PERIODE_BASSE}}', $prixRoulotteRougeBas,
			str_replace('{{PRIX_ROULOTTE_ROUGE_PERIODE_HAUTE}}', $prixRoulotteRougeHaut,
			str_replace('{{PRIX_ROULOTTE_BLEUE_PERIODE_BASSE}}', $prixRoulotteBleueBas,
			str_replace('{{PRIX_ROULOTTE_BLEUE_PERIODE_HAUTE}}', $prixRoulotteBleueHaut,
			str_replace('{{PRIX_TENTE_SAFARI_PERIODE_BASSE}}', $prixTenteSafariBas,
			str_replace('{{PRIX_TENTE_SAFARI_PERIODE_HAUTE}}', $prixTenteSafariHaut,
			$newContenu)))))))))))))));
	
		//Nombre
		$newContenu = str_replace('{{NB_ADULTES}}', $nombreAdultes,
			str_replace('{{NB_ENFANTS}}', $nombreEnfants,
			str_replace('{{NB_ANIMAUX}}', $nombreAnimaux,
			str_replace('{{NB_TARIF1}}', $nombreTarif1,
			str_replace('{{NB_TARIF2}}', $nombreTarif2,
			str_replace('{{NB_TARIF3}}', $nombreTarif3,
			str_replace('{{ELECTRICITE}}', $electricite,
			str_replace('{{NB_VEHICULES_SUPP}}', $nombreVehiculesSupp,
			str_replace('{{NB_VISITEURS}}', $nombreVisiteurs,
			str_replace('{{NB_ROULOTTE_ROUGE_PERIODE_BASSE}}', $nombreRoulotteRougeBas,
			str_replace('{{NB_ROULOTTE_ROUGE_PERIODE_HAUTE}}', $nombreRoulotteRougeHaut,
			str_replace('{{NB_ROULOTTE_BLEUE_PERIODE_BASSE}}', $nombreRoulotteBleueBas,
			str_replace('{{NB_ROULOTTE_BLEUE_PERIODE_HAUTE}}', $nombreRoulotteBleueHaut,
			str_replace('{{NB_TENTE_SAFARI_PERIODE_BASSE}}', $nombreTenteSafariBas,
			str_replace('{{NB_TENTE_SAFARI_PERIODE_HAUTE}}', $nombreTenteSafariHaut,
			$newContenu)))))))))))))));

		//Sous total
		$sousTotalAdulte = $nombreAdultes * $prixAdulte;
		$sousTotalEnfant = $nombreEnfants * $prixEnfant;
		$sousTotalAnimal = $nombreAnimaux * $prixAnimal;
		$sousTotalTarif1 = $nombreTarif1 * $prixTarif1;
		$sousTotalTarif2 = $nombreTarif2 * $prixTarif2;
		$sousTotalTarif3 = $nombreTarif3 * $prixTarif3;
		$sousTotalElectricite = $electricite * $prixElectricite;
		$sousTotalVehiculeSupp = $nombreVehiculesSupp * $prixVehiculeSupp;
		$sousTotalVisiteur = $nombreVisiteurs * $prixVisiteur;
		$sousTotalRoulotteRougeBas = $nombreRoulotteRougeBas * $prixRoulotteRougeBas;
		$sousTotalRoulotteRougeHaut = $nombreRoulotteRougeHaut * $prixRoulotteRougeHaut;
		$sousTotalRoulotteBleueBas = $nombreRoulotteBleueBas * $prixRoulotteBleueBas;
		$sousTotalRoulotteBleueHaut = $nombreRoulotteBleueHaut * $prixRoulotteBleueHaut;
		$sousTotalTenteSafariBas = $nombreTenteSafariBas * $prixTenteSafariBas;
		$sousTotalTenteSafariHaut = $nombreTenteSafariHaut * $prixTenteSafariHaut;
		$sousTotalNuitees = $sousTotalAdulte + $sousTotalEnfant + $sousTotalAnimal +
		$sousTotalTarif1 + $sousTotalTarif2 +
		$sousTotalTarif3 + $sousTotalElectricite +
		$sousTotalVehiculeSupp + $sousTotalVisiteur;
		$totalSejour = $sousTotalNuitees * $nbNuitees;
		$totalSejourRoulottes = $sousTotalRoulotteRougeBas + $sousTotalRoulotteRougeHaut +
		$sousTotalRoulotteBleueBas + $sousTotalRoulotteBleueHaut +
		$sousTotalTenteSafariBas + $sousTotalTenteSafariHaut;
		$remise = floatval($facture->getRemiseExceptionnelle());
		if (is_null($remise) or $remise == 0) {
			//Si la remise est nulle, on cache le libelle
			$strRemise = ' ';
			$libelleRemise = '';
			//On réduit la div avec la remise
			$tailleDiv = '1.9';
			$divTaillePrixRemiseC = 'style="position:absolute;margin-left:-4.3pt;margin-top:.3pt;width:93.4pt;height:21.4pt';
			$divTaillePrixRemiseApresC = 'style="position:absolute;margin-left:-4.3pt;margin-top:.3pt;width:' . $tailleDiv . 'pt;height:' . $tailleDiv . 'pt';
			$newContenu = str_replace($divTaillePrixRemiseC, $divTaillePrixRemiseApresC, $newContenu);
			$divTaillePrixRemiseF = 'style="position:absolute;margin-left:-4.3pt;margin-top:5.4pt;width:93.4pt;height:21.4pt';
			$divTaillePrixRemiseApresF = 'style="position:absolute;margin-left:-4.3pt;margin-top:5.4pt;width:' . $tailleDiv . 'pt;height:' . $tailleDiv . 'pt';
			$newContenu = str_replace($divTaillePrixRemiseF, $divTaillePrixRemiseApresF, $newContenu);
		}
		else {
			$strRemise = '- ' . $remise;
			$libelleRemise = self::LIBELLE_REMISE_EXCEPTIONNELLE;
			$totalSejour -= $remise;
			$totalSejourRoulottes -= $remise;
		}

		$newContenu = str_replace('{{SOUS_TOTAL_ADULTE}}', $sousTotalAdulte,
			str_replace('{{SOUS_TOTAL_ENFANT}}', $sousTotalEnfant,
			str_replace('{{SOUS_TOTAL_ANIMAL}}', $sousTotalAnimal,
			str_replace('{{SOUS_TOTAL_TARIF1}}', $sousTotalTarif1,
			str_replace('{{SOUS_TOTAL_TARIF2}}', $sousTotalTarif2,
			str_replace('{{SOUS_TOTAL_TARIF3}}', $sousTotalTarif3,
			str_replace('{{SOUS_TOTAL_ELECTRICITE}}', $sousTotalElectricite,
			str_replace('{{SOUS_TOTAL_VEHICULE_SUPP}}', $sousTotalVehiculeSupp,
			str_replace('{{SOUS_TOTAL_VISITEUR}}', $sousTotalVisiteur,
			str_replace('{{SOUS_TOTAL_ROULOTTE_ROUGE_PERIODE_BASSE}}', $sousTotalRoulotteRougeBas,
			str_replace('{{SOUS_TOTAL_ROULOTTE_ROUGE_PERIODE_HAUTE}}', $sousTotalRoulotteRougeHaut,
			str_replace('{{SOUS_TOTAL_ROULOTTE_BLEUE_PERIODE_BASSE}}', $sousTotalRoulotteBleueBas,
			str_replace('{{SOUS_TOTAL_ROULOTTE_BLEUE_PERIODE_HAUTE}}', $sousTotalRoulotteBleueHaut,
			str_replace('{{SOUS_TOTAL_TENTE_SAFARI_PERIODE_BASSE}}', $sousTotalTenteSafariBas,
			str_replace('{{SOUS_TOTAL_TENTE_SAFARI_PERIODE_HAUTE}}', $sousTotalTenteSafariHaut,
			str_replace('{{SOUS_TOTAL_NUITEES}}', $sousTotalNuitees,
			str_replace('{{PRIX_REMISE_EXCEPTIONNELLE}}', $strRemise,
			str_replace('{{LABEL_REMISE_EXCEPTIONNELLE}}', $libelleRemise,
			str_replace('{{TOTAL_SEJOUR}}', $totalSejour,
			str_replace('{{TOTAL_SEJOUR_ROULOTTES}}', $totalSejourRoulottes,
			$newContenu))))))))))))))))))));

		return $newContenu;
	}
}

?>