<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Enregistre l'emplacement d'une réservation
 * @author Arnaud DUPUIS
 * @param string refFiche Id de la réservation (POST)
 * @param integer coordonneesX Coordonnées X de l'emplacement (POST)
 * @param integer coordonneesY Coordonnées Y de l'emplacement (POST)
 * @return string Renvoie true si succes, false sinon
 */
$retour = false;

$planningCamping = new PlanningCampingController();

//Enregistrement du numéro de l'emplacement
if ((array_key_exists('refFiche', $_POST)) and (array_key_exists('numeroEmplacement', $_POST))) {
	$refFiche = $_POST['refFiche'];
	$numeroEmplacement = $_POST['numeroEmplacement'];

	$retour = $planningCamping->enregistrerNumeroEmplacement($refFiche, $numeroEmplacement);
}

//Enregistrement des coordonnées de l'emplacement
if ((array_key_exists('refFiche', $_POST)) and (array_key_exists('coordonneesX', $_POST))
and (array_key_exists('coordonneesY', $_POST))) {
	$refFiche = $_POST['refFiche'];
	$coordonneesX = $_POST['coordonneesX'];
	$coordonneesY = $_POST['coordonneesY'];

	$retour = $planningCamping->enregistrerEmplacement($refFiche, $coordonneesX, $coordonneesY);
}

//Retour en json
echo $retour;
?>