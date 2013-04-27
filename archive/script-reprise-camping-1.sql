ALTER TABLE `reservation` ADD `reference` INT(11) NOT NULL COMMENT 'Référence de la réservation' AFTER `id`;

ALTER TABLE `reservation` ADD `arrhes` INT NULL COMMENT 'Arrhes sur la réservation' AFTER `piece_id_presentee`;

ALTER TABLE `reservation` ADD `coordonnees_x_emplacement` INT NOT NULL COMMENT 'Coordonnées x de l''emplacement' AFTER `reference_facture` ,
ADD `coordonnees_y_emplacement` INT NOT NULL COMMENT 'Coordonnées y de l''emplacement' AFTER `coordonnees_x_emplacement`;

ALTER TABLE `reservation` ADD `numero_emplacement` INT NULL COMMENT 'Numéro de l''emplacement' AFTER `reference_facture`;

-- Vérifier que les dates sont vides! --
UPDATE `reservation` SET `date_creation` = NOW() WHERE `date_creation` = '0000-00-00 00:00:00';
UPDATE `client` SET `date_creation` = NOW() WHERE `date_creation` = '0000-00-00 00:00:00';

DELIMITER $$
CREATE TRIGGER before_create_client BEFORE INSERT ON client
    FOR EACH ROW
	BEGIN
		SET new.date_creation = NOW();
END$$

CREATE TRIGGER before_update_client BEFORE UPDATE ON client
    FOR EACH ROW
	BEGIN
		SET new.date_modification = NOW();
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER before_create_reservation BEFORE INSERT ON reservation
    FOR EACH ROW
	BEGIN
		SET new.date_creation = NOW();
END$$

CREATE TRIGGER before_update_reservation BEFORE UPDATE ON reservation
    FOR EACH ROW
	BEGIN
		SET new.date_modification = NOW();
END$$
DELIMITER ;

-- Vérifier les références des réservations et des clients!!! --
----------------------------------------------------------------

ALTER TABLE client ADD CONSTRAINT uc_reference_client UNIQUE (reference);
ALTER TABLE reservation ADD CONSTRAINT uc_reference_reservation UNIQUE (reference);