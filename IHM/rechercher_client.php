<?php
include_once '../translation.php';
include_once '../Controller/RechercherClientController.php';

/**
 * Renvoie les paramètres pour l'autocomplétion
 * @author Arnaud DUPUIS
 * @return string $value Concaténation des infos du Client
 * @return string $label Nom affiché
 */

$retour = null;
$criteres = null;

//On recherche par critères
if (array_key_exists('prenom', $_GET)) {
	$criteres['prenom'] = $_GET['prenom'] . '%';
}
if (array_key_exists('nom', $_GET)) {
	$criteres['nom'] = $_GET['nom'] . '%';
}

//Pas de recherche si pas de critères
if(!is_null($criteres)) {
	$rechercheClient = new RechercherClientController();
	
	$retour = $rechercheClient->rechercherClient($criteres);
}

//Retour en json
echo json_encode($retour);
?>