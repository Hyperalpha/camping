<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Exporte la base de donn�es (var_dump) et renvoie le fichier
 * Attention aux probl�mes de s�curit� de cette fonction (ne pas utiliser
 * sur internet)
 * @author Arnaud DUPUIS
 */

$planningCamping = new PlanningCampingController();
	
$retour = $planningCamping->exporterBaseDeDonnees();

?>