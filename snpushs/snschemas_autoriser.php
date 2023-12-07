<?php

/***************************************************************************\
 *  SN Suite, suite de plugins pour SPIP                                   *
 *  Copyright © depuis 2014                                                *
 *  Simon N                                                            *
 *                                                                         *
 *  Ce programme est un logiciel libre distribué sous licence GNU/GPL.     *
 *  Pour plus de détails voir l'aide en ligne.                             *
 *  https://www.snsuite.net                                                *
\**************************************************************************/

/**
 * Définit les autorisations du plugin SN Abonnements
 *
 * @package SPIP\SNabonnements\Autorisations
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function snschemas_autoriser() {
}
function autoriser_snschemas_voir_dist($faire, $type, $id, $qui, $opt) {
	return ( $qui['statut'] == '0minirezo ');
}
function autoriser_snschemas_menu_dist($faire, $type, $id, $qui, $opt) {
	return ( $qui['statut'] == '0minirezo ');
}
function autoriser_snschema_instituer_dist($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_snschema_modifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_snschema_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_snschema_voir_dist($faire, $type, $id, $qui, $opt) {
	return ( $qui['statut'] == '0minirezo ');
}
function autoriser_snschema_exporter_dist($faire, $type, $id, $qui, $opt) {
	return false;
}
