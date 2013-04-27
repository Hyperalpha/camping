<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Exporte une réservation dans un fichier (Word ou Excel)
 * @author Arnaud DUPUIS
 * @param idReservation Id de la réservation à exporter
 */

//On récupère les données de réservation
if (array_key_exists('idFiche', $_GET)) {
	$idReservation = $_GET['idFiche'];
	
	$planningCamping = new PlanningCampingController();
	
	$retour = $planningCamping->exporterReservation($idReservation);
}

?>