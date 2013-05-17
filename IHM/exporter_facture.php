<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Exporte la facture d'une réservation dans un fichier (Word ou Excel)
 * @author Arnaud DUPUIS
 * @param idReservation Id de la réservation à exporter
 */

//On récupère l'identifiant de la réservation
if ((array_key_exists('idFiche', $_GET)) and (array_key_exists('regenererFacture', $_GET))) {
	$idReservation = $_GET['idFiche'];
	$regenererFacture = $_GET['regenererFacture'];
	
	if ($regenererFacture == 'true') {
		$bRegenererFacture = true;
	}
	else {
		$bRegenererFacture = false;
	}
	
	$planningCamping = new PlanningCampingController();
	
	$retour = $planningCamping->exporterFacture($idReservation, $bRegenererFacture);
}

?>