<?php

/**
 * Copyright Arnaud DUPUIS 2012
 * @author Arnaud DUPUIS
 *
 */
class CommunModel {

	/**
	 * Supprime un r�pertoire et son contenu (fichiers et sous-r�pertoires)
	 * @author Arnaud DUPUIS
	 * @param string $cheminRepertoire Chemin complet vers le r�pertoire � supprimer
	 */
	public static function supprimerRepertoire($cheminRepertoire) {

		//On rajoute le "/" � la fin
		if ($cheminRepertoire[strlen($cheminRepertoire)-1] != '/') {
			$cheminRepertoire .= '/';
		}

		if (is_dir($cheminRepertoire)) {
			//On supprime le contenu du r�pertoire
			self::supprimerContenuRepertoire($cheminRepertoire);
			//On supprime le r�pertoire
			rmdir($cheminRepertoire);
		}
		else {
			//Si c'est un fichier, on le supprime
			unlink($cheminRepertoire);
		}
	}

	/**
	 * Supprime le contenu d'un r�pertoire (fichiers et sous-r�pertoires)
	 * @author Arnaud DUPUIS
	 * @param string $cheminRepertoire Chemin complet vers le r�pertoire
	 * dont le contenu va �tre supprim�
	 */
	public static function supprimerContenuRepertoire($cheminRepertoire) {

		//On rajoute le "/" � la fin
		if ($cheminRepertoire[strlen($cheminRepertoire)-1] != '/') {
			$cheminRepertoire .= '/';
		}

		if (is_dir($cheminRepertoire)) {
			//Ouverture du r�pertoire
			$dir = opendir($cheminRepertoire);
			while ($f = readdir($dir)) {
				//On ne traite pas les r�pertoire "." et ".."
				if ($f != '.' && $f != '..') {
					$fichier = $cheminRepertoire . $f;
					if (is_dir($fichier)) {
						//Si le fichier est un r�pertoire => r�cursivit�
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
	 * G�n�re un dump de la base de donn�es � l'endroit sp�cifi� en param�tre
	 * @author adupuis
	 * @param string $cheminExecMysql Chemin complet de l'ex�cutable mysql
	 * @return string Url relative (par rapport � ce fichier vers le 
	 * fichier contenant le dump mySql 
	 */
	public function dumpBDD($cheminExecMysql) {
		$DS = DIRECTORY_SEPARATOR;
		$repertoireTmp = dirname(__FILE__) . $DS . 'tmp';
		$nomDump = 'dump_bdd_camping_' . date('Ymd') . '.sql';
		
		//Ex�cution du dump
		exec($cheminExecMysql . ' -h localhost -u root camping > ' . $repertoireTmp . $DS . $nomDump);
		
		return 'tmp/' . $nomDump;
	}
}

?>