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

function snpushs_autoriser() {
}
function autoriser_snpushs_voir_dist($faire, $type, $id, $qui, $opt) {
	$aut = false;
	if( $qui['statut'] == '0minirezo'){
		$aut = true;
	} elseif( $qui['statut'] == '1comite' ){
		if($GLOBALS['meta']['sn_pushs_autoriser_redacteurs'] === 'on'){
			$aut = true;
		}
	}
	return $aut;
}
function autoriser_snpushs_menu_dist($faire, $type, $id, $qui, $opt) {
	$aut = false;
	if( $qui['statut'] == '0minirezo'){
		$aut = true;
	} elseif( $qui['statut'] == '1comite' ){
		if($GLOBALS['meta']['sn_pushs_autoriser_redacteurs'] === 'on'){
			$aut = true;
		}
	}
	return $aut;
}
function autoriser_snpush_modifier_dist($faire, $type, $id, $qui, $opt) {
	if ( !intval($id) OR !$qui['id_auteur'] OR !autoriser('ecrire', '', '', $qui) ) {
		return false;
	}
	return ( $qui['statut'] == '0minirezo' || $qui['statut'] == '1comite' );
}
function autoriser_snpush_supprimer_dist($faire, $type, $id, $qui, $opt) {
	if ( !intval($id) OR !$qui['id_auteur'] OR !autoriser('ecrire', '', '', $qui) ) {
		return false;
	}
	return autoriser('modifier', 'snpush', $id, $qui, $opt);
}
function autoriser_snpush_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'snpush', $id, $qui, $opt);
}
function autoriser_snpush_exporter_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'snpush', $id, $qui, $opt);
}
