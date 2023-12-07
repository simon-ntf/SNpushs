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
 * Formulaire de configuration SN Pushs
 *
 * @plugin SN Pushs
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_snpushs_charger_dist(){

	include_spip('inc/sn_const');
	$sn_const_spip_objets_non_pushables = sn_global_spip_objets_non_pushables();

	$valeurs = [];
	$autorisation = autoriser('modifier', 'snpushs');
	if ($autorisation === true) {
		$schema_defaut_objets = explode(',',$GLOBALS['meta']['sn_pushs_schema_defaut_objets']);
		$liste_schemas_articles = sn_const_options_trads('schema_gambit','E','snpushs');
		unset($liste_schemas_articles['secteur']);
		$liste_schemas_rubriques = sn_const_options_trads('schema_gambit','E','snpushs');
		unset($liste_schemas_articles['parent']);
		$valeurs = [
			'liste_schemas_articles' => $liste_schemas_articles,
			'liste_schemas_rubriques' => $liste_schemas_rubriques,
			'liste_objets_exclus' => $sn_const_spip_objets_non_pushables,
			'sn_pushs_autoriser_redacteurs' => $GLOBALS['meta']['sn_pushs_autoriser_redacteurs'],
			'sn_pushs_alertes_globales' => $GLOBALS['meta']['sn_pushs_alertes_globales'],
			'sn_pushs_schema_defaut' => $GLOBALS['meta']['sn_pushs_schema_defaut'],
			'sn_pushs_schema_defaut_objets' => $schema_defaut_objets,
			'sn_pushs_schema_articles' => $GLOBALS['meta']['sn_pushs_schema_articles'],
			'sn_pushs_schema_rubriques' => $GLOBALS['meta']['sn_pushs_schema_rubriques'],
		];
	} else{ return false; }

	return $valeurs;
}

function formulaires_configurer_snpushs_verifier_dist(){

	include_spip('inc/sn_regexr');
	include_spip('inc/sn_const');
	$sn_liste_objets_pushables = sn_global_spip_objets_pushables();
	$erreurs = [];
	$liste_schemas_articles = sn_const_options_trads('schema_gambit','E','snpushs');
	unset($liste_schemas_articles['secteur']);
	if(!isset($liste_schemas_articles[_request('sn_pushs_schema_articles')])){
		$erreurs['sn_pushs_schema_articles'] = _T('sncore:regex_gen');
	}
	$liste_schemas_rubriques = sn_const_options_trads('schema_gambit','E','snpushs');
	unset($liste_schemas_articles['parent']);
	if(!isset($liste_schemas_rubriques[_request('sn_pushs_schema_rubriques')])){
		$erreurs['sn_pushs_schema_rubriques'] = _T('sncore:regex_gen');
	}
	if(_request('sn_pushs_schema_defaut_objets')){
		if(is_array(_request('sn_pushs_schema_defaut_objets'))){
			foreach(_request('sn_pushs_schema_defaut_objets') as $cle => $spip_obj){
				if(strlen($spip_obj)>6 && strlen($spip_obj)<48){
					if(in_array($spip_obj,$sn_liste_objets_pushables)){
					} else{
						$erreurs['sn_pushs_schema_defaut_objets'] .= _T('sncore:regex_gen');
					}
				}
			}
		}
	}
	if(_request('sn_pushs_autoriser_redacteurs')){
		if(sn_verif_bool_on(_request('sn_pushs_autoriser_redacteurs')) === true){
		} else {
		$erreurs['sn_pushs_autoriser_redacteurs'] = _T('sncore:regex_gen');
		}
	}
	if(_request('sn_pushs_alertes_globales')){
		if(sn_verif_bool_on(_request('sn_pushs_alertes_globales')) === true){
		} else {
			$erreurs['sn_pushs_alertes_globales'] = _T('sncore:regex_gen');
		}
	}
	if(_request('sn_pushs_schema_defaut')){
		if(sn_verif_bool_on(_request('sn_pushs_schema_defaut')) === true){} else {
			$erreurs['sn_pushs_schema_defaut'] = _T('sncore:regex_gen');
		}
	}
	return $erreurs;

}

function formulaires_configurer_snpushs_traiter_dist(){

	$retours = [];
	$retours['message_erreur'] = _T('sncore:message_erreur_defaut');
	$autorisation = autoriser('modifier', 'snpushs');
	if ($autorisation === true) {
		include_spip('inc/config');
		$schema_defaut_objets = implode(',',_request('sn_pushs_schema_defaut_objets'));
		set_request('sn_pushs_schema_defaut_objets',$schema_defaut_objets);
		appliquer_modifs_config();
		$retours = [];
		$retours['message_ok'] = _T('config_info_enregistree');
	}
	return $retours;
	
}
