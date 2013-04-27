<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Charge la popup de réglages avec les bonnes valeurs
 * @author Arnaud DUPUIS
 * @return string Renvoie la popup chargée
 */

$planningCamping = new PlanningCampingController();

$retour = $planningCamping->chargerPopupReglages();

//Retour en json
echo $retour;
?>