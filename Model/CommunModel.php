<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class CommunModel {

	/**
	 * Supprime un répertoire et son contenu (fichiers et sous-répertoires)
	 * @author Arnaud DUPUIS
	 * @param string $cheminRepertoire Chemin complet vers le répertoire à supprimer
	 */
	public static function supprimerRepertoire($cheminRepertoire) {

		//On rajoute le "/" à la fin
		if ($cheminRepertoire[strlen($cheminRepertoire)-1] != '/') {
			$cheminRepertoire .= '/';
		}

		if (is_dir($cheminRepertoire)) {
			//On supprime le contenu du répertoire
			self::supprimerContenuRepertoire($cheminRepertoire);
			//On supprime le répertoire
			rmdir($cheminRepertoire);
		}
		else {
			//Si c'est un fichier, on le supprime
			unlink($cheminRepertoire);
		}
	}

	/**
	 * Supprime le contenu d'un répertoire (fichiers et sous-répertoires)
	 * @author Arnaud DUPUIS
	 * @param string $cheminRepertoire Chemin complet vers le répertoire
	 * dont le contenu va être supprimé
	 */
	public static function supprimerContenuRepertoire($cheminRepertoire) {

		//On rajoute le "/" à la fin
		if ($cheminRepertoire[strlen($cheminRepertoire)-1] != '/') {
			$cheminRepertoire .= '/';
		}

		if (is_dir($cheminRepertoire)) {
			//Ouverture du répertoire
			$dir = opendir($cheminRepertoire);
			while ($f = readdir($dir)) {
				//On ne traite pas les répertoire "." et ".."
				if ($f != '.' && $f != '..') {
					$fichier = $cheminRepertoire . $f;
					if (is_dir($fichier)) {
						//Si le fichier est un répertoire => récursivité
						self::supprimerRepertoire($fichier);
					}
					else {
						//Suppression du fichier
						unlink($fichier);
					}
				}
			}
			closedir($dir);
		}
	}
	
	/**
	 * Génère un dump de la base de données à l'endroit spécifié en paramètre
	 * @author adupuis
	 * @param string $cheminExecMysql Chemin complet de l'exécutable mysql
	 * @return string Url relative (par rapport à ce fichier vers le 
	 * fichier contenant le dump mySql 
	 */
	public function dumpBDD($cheminExecMysql) {
		$DS = DIRECTORY_SEPARATOR;
		$repertoireTmp = dirname(__FILE__) . $DS . 'tmp';
		$nomDump = 'dump_bdd_camping_' . date('Ymd') . '.sql';
		
		//Exécution du dump
		exec($cheminExecMysql . ' -h localhost -u root camping > ' . $repertoireTmp . $DS . $nomDump);
		
		return 'tmp/' . $nomDump;
	}
}

?>