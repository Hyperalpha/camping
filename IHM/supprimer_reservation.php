<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Supprime une réservation
 * @author Arnaud DUPUIS
 * @param string idFiche Id de la réservation à supprimer
 * @return Boolean Renvoie true si l'opération est un succès, false sinon
 */
$retour = false;

//On récupère les données de réservation
if (array_key_exists('idFiche', $_POST)) {
	$idReservation = $_POST['idFiche'];
	
	$planningCamping = new PlanningCampingController();
	
	$retour = $planningCamping->supprimerReservation($idReservation);
}

//Retour en json
echo $retour;
?>