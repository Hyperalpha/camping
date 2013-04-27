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
			//Ouverture du répertoire
			$dir = opendir($cheminRepertoire);
			while ($f = readdir($dir)) {
				//On ne traite pas les répertoire "." et ".."
				if ($f != '.' && $f != '..') {
					$fichier = $cheminRepertoire . $f;
					if (is_dir($fichier)) {
						//Si le fichier est un répertoire => récursivité
						sup_repertoire($fichier);
					}
					else {
						//Suppression du fichier
						unlink($fichier);
					}
				}
			}
			closedir($dir);
			//On supprime le répertoire
			rmdir($cheminRepertoire);
		}
		else {
			//Si c'est un fichier, on le supprime
			unlink($cheminRepertoire);
		}
	}
}

?>