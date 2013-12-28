<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Enregistre la popup de réglages
 * @author Arnaud DUPUIS
 * @param array POST Valeurs du formulaire passées en POST
 */
$codeValeursARecuperer = array('Prix/nuitée_campeur_adulte', 'Prix/nuitée_campeur_enfant',
	'Prix/nuitée_animal', 'Prix/nuitée_tarif1', 'Prix/nuitée_tarif2', 
	'Prix/nuitée_emplacement_tarif3', 'Prix/nuitée_électricité', 
	'Prix/nuitée_véhicule_supplémentaire', 'Prix/nuitée_visiteur', 
	'Prix_roulotte_rouge_période_basse', 'Prix_roulotte_rouge_période_haute', 
	'Prix_roulotte_bleue_période_basse', 'Prix_roulotte_bleue_période_haute',
	'Prix_tente_safari_période_basse', 'Prix_tente_safari_période_haute',
	'Date_de_début_du_tableau_des_réservation', 'Date_de_fin_du_tableau_des_réservation', 
	'Date_de_début_de_la_période_haute_des_roulottes',
	'Date_de_fin_de_la_période_haute_des_roulottes');
$valeursARecuperer = array(null, null, null, null, null, null, null, null, null, null,
null, null, null, null, null, null);

foreach ($codeValeursARecuperer as $key => $codeValeurs) {
	if (array_key_exists($codeValeurs, $_POST)) {
		$valeursARecuperer[$key] = $_POST[$codeValeurs];
	}
}

//Construction d'un objet stdClass à passer au contrôleur
$stdReglages = new \stdClass();
$stdReglages->prixAdulte = $valeursARecuperer[0];
$stdReglages->prixEnfant = $valeursARecuperer[1];
$stdReglages->prixAnimal = $valeursARecuperer[2];
$stdReglages->prixTarif1 = $valeursARecuperer[3];
$stdReglages->prixTarif2 = $valeursARecuperer[4];
$stdReglages->prixTarif3 = $valeursARecuperer[5];
$stdReglages->prixElectricite = $valeursARecuperer[6];
$stdReglages->prixVehiculeSupp = $valeursARecuperer[7];
$stdReglages->prixVisiteur = $valeursARecuperer[8];
$stdReglages->prixRoulottesRougePeriodeBasse = $valeursARecuperer[9];
$stdReglages->prixRoulottesRougePeriodeHaute = $valeursARecuperer[10];
$stdReglages->prixRoulottesBleuePeriodeBasse = $valeursARecuperer[11];
$stdReglages->prixRoulottesBleuePeriodeHaute = $valeursARecuperer[12];
$stdReglages->prixTentesSafariPeriodeBasse = $valeursARecuperer[13];
$stdReglages->prixTentesSafariPeriodeHaute = $valeursARecuperer[14];
$stdReglages->dateDebutAffichageReservations = $valeursARecuperer[15];
$stdReglages->dateFinAffichageReservations = $valeursARecuperer[16];
$stdReglages->dateDebutPeriodeHauteRoulottes = $valeursARecuperer[17];
$stdReglages->dateFinPeriodeHauteRoulottes = $valeursARecuperer[18];

$planningCamping = new PlanningCampingController();
$planningCamping->enregistrerReglages($stdReglages);

?>