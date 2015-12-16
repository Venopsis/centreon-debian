-- Insert default directory images
INSERT INTO `options` (`key`, `value`)
SELECT * FROM (SELECT 'nagios_path_img', '@INSTALL_DIR_CENTREON@/www/img/media/') AS tmp
WHERE NOT EXISTS (SELECT `key` FROM `options` WHERE `key` = 'nagios_path_img' ) LIMIT 1;

-- Change version of Centreon
UPDATE `informations` SET `value` = '2.7.0' WHERE CONVERT( `informations`.`key` USING utf8 )  = 'version' AND CONVERT ( `informations`.`value` USING utf8 ) = '2.7.0-RC2' LIMIT 1;

-- Delete multiple topology, exept once
DELETE FROM `topology` WHERE `topology_page` = '60807' AND topology_id in (SELECT * FROM (SELECT topology_id FROM `topology` WHERE `topology_page` = '60807' LIMIT 1, 100) as t);

-- Add Filter for Service Grid in Topology
UPDATE topology SET topology_url_opt = '&o=svcOV_pb' WHERE topology_page = 20204;