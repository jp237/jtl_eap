ALTER TABLE `xplugin_jtl_eap_kundengruppen` ADD `nBoni` VARCHAR( 1 ) NOT NULL DEFAULT '1', ADD `nIdent` VARCHAR( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `xplugin_jtl_eap_fulllog` ADD `customer_firma` VARCHAR( 128 ) NOT NULL , ADD `cArt` VARCHAR( 32 ) NOT NULL ;
ALTER TABLE `xplugin_jtl_eap_fulllog` CHANGE `error` `error` VARCHAR( 2048 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE `xplugin_jtl_eap_fulllog` ADD `responseCode` INT( 5 ) NOT NULL ;
ALTER TABLE `xplugin_jtl_eap_fulllog` ADD `responseText` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `xplugin_jtl_eap_kundengruppen` ADD `nIdentMove` INT( 1 ) NOT NULL DEFAULT '0';
CREATE TABLE `xplugin_jtl_eap_identcheck_log` (`kKunde` INT NOT NULL ,`tstamp` DATETIME NOT NULL  ,`handle` VARCHAR( 32 ) NOT NULL) ;