<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Exporte la base de données (var_dump) et renvoie le fichier
 * Attention aux problèmes de sécurité de cette fonction (ne pas utiliser
 * sur internet)
 * @author Arnaud DUPUIS
 */

$planningCamping = new PlanningCampingController();
	
$retour = $planningCamping->exporterBaseDeDonnees();

?>