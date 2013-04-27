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
			//Ouverture du r�pertoire
			$dir = opendir($cheminRepertoire);
			while ($f = readdir($dir)) {
				//On ne traite pas les r�pertoire "." et ".."
				if ($f != '.' && $f != '..') {
					$fichier = $cheminRepertoire . $f;
					if (is_dir($fichier)) {
						//Si le fichier est un r�pertoire => r�cursivit�
						sup_repertoire($fichier);
					}
					else {
						//Suppression du fichier
						unlink($fichier);
					}
				}
			}
			closedir($dir);
			//On supprime le r�pertoire
			rmdir($cheminRepertoire);
		}
		else {
			//Si c'est un fichier, on le supprime
			unlink($cheminRepertoire);
		}
	}
}

?>