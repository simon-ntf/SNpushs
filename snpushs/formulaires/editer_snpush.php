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
 * Formulaire pour éditer un bloc push
 *
 * @plugin SN Pushs
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_snpush_charger_dist($id_snpush='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$valeurs = [];
	$autorisation = autoriser('modifier', 'snpush');
	if ($autorisation === true) {
		$valeurs = formulaires_editer_objet_charger('snpush',$id_snpush, $id_rubrique, $retour, $lier_trad, $config_fonc, $row, $hidden);
		$valeurs['liste_snpushs_statuts'] = [
			'publie' => _T('snpushs:statut_publie'),
			'archive' => _T('snpushs:statut_archive'),
			'prepa' => _T('snpushs:statut_prepa'),
		];
		$valeurs['liste_snpushs_types'] = sn_const_options_trads('snpush_type','E','snpushs');
		return $valeurs;
	}
	return false;

}

function formulaires_editer_snpush_identifier_dist($id_snpush='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_snpush)));
}

function formulaires_editer_snpush_verifier_dist($id_snpush='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	include_spip('inc/sn_regexr');

	$erreurs = [];

	$liste_pushtypes = sn_const_options_trads('snpush_type','E','snpushs');

	$pushtype = 'aucun';

	if (_request('pushtype')){ if(!isset($liste_pushtypes[_request('pushtype')])){
		$erreurs['pushtype'] = _T('sncore:regex_gen');
	}}
	if (!isset($erreurs['pushtype'])){ $pushtype = _request('pushtype'); }

	$liste_statuts = [ 'publie' => _T('snpushs:statut_publie'), 'archive' => _T('snpushs:statut_archive'), 'prepa' => _T('snpushs:statut_prepa')];
	if(!isset($liste_statuts[_request('statut')])){
		$erreurs['statut'] = _T('sncore:regex_gen');
	}
	if (!_request('titre')){
		$erreurs['titre'] = _T('info_obligatoire');
	} elseif (!preg_match(sn_regex_txt_etendu(244,0),_request('titre'))){
		$erreurs['titre'] = _T('sncore:regex_txt_nb',['nb'=>'244']);
	}
	if (_request('latitude')){ if(!preg_match(sn_regex_geoloc_lat(),_request('latitude'))){
		$erreurs['latitude'] = _T('sncore:regex_lat');
	}}
	if (_request('longitude')){ if(!preg_match(sn_regex_geoloc_lon(),_request('longitude'))){
		$erreurs['longitude'] = _T('sncore:regex_lon');
	}}
	/*if (_request('lien')){
		$lien = filter_var(_request('lien'), FILTER_SANITIZE_URL);
		if(strlen(_request('lien')) > 244){
			$erreurs['lien'] = _T('sncore:regex_txt_longueur_nb',[nb=>244]);
		} elseif (!filter_var(_request('lien'), FILTER_VALIDATE_URL)){
			$erreurs['lien'] = _T('sncore:regex_url');
		}
	}*/
	if (_request('resume')){
		if(!preg_match(sn_regex_txt_etendu(244,0),_request('resume'))){
			$erreurs['resume'] = _T('sncore:regex_txt_etendu_nb',['nb'=>'244']);
		}
	}
	/*if (_request('mel')){ if(!filter_var(_request('mel'), FILTER_VALIDATE_EMAIL)){
		$erreurs['mel'] = _T('sncore:regex_email');
	}}*/
	if (_request('urgence')){
		if(sn_verif_bool_on(_request('urgence')) === true){
		} else {
			$erreurs['urgence'] = _T('sncore:regex_gen');
		}
	}

	if (!isset($erreurs['pushtype'])){
		if ($pushtype === 'article'){
			if (!_request('cible_article')){
				$erreurs['cible_article'] = _T('info_obligatoire');
			} else if (!preg_match(sn_regex_int(21),_request('cible_article'))){
				$erreurs['cible_article'] = _T('sncore:regex_id_article');
			} else if ($req = sql_getfetsel('id_article','spip_articles','id_article='.intval(_request('cible_article')))){
				// cas valide on ne fait rien
			} else {
				$erreurs['cible_article'] = _T('sncore:erreur_objet_inexistant_article');
			}
		} else if ($pushtype === 'auteur'){
			if (!_request('cible_auteur')){
				$erreurs['cible_auteur'] = _T('info_obligatoire');
			} else if (!preg_match(sn_regex_int(21),_request('cible_auteur'))){
				$erreurs['cible_auteur'] = _T('sncore:regex_id_auteur');
			} else if ($req = sql_getfetsel('id_auteur','spip_auteurs','id_auteur='.intval(_request('cible_auteur')))){
				// cas valide on ne fait rien
			} else {
				$erreurs['cible_auteur'] = _T('sncore:erreur_objet_inexistant_auteur');
			}
		} else if ($pushtype === 'rubrique'){
			if (!_request('cible_rubrique')){
				$erreurs['cible_rubrique'] = _T('info_obligatoire');
			} else if (!preg_match(sn_regex_int(21),_request('cible_rubrique'))){
				$erreurs['cible_rubrique'] = _T('sncore:regex_id_rubrique');
			} else if ($req = sql_getfetsel('id_rubrique','spip_rubriques','id_rubrique='.intval(_request('cible_rubrique')))){
				// cas valide on ne fait rien
			} else {
				$erreurs['cible_rubrique'] = _T('sncore:erreur_objet_inexistant_rubrique');
			}
		}
	}

	if (_request('css_classes')){
		if(!preg_match(sn_regex_domclasses(),_request('css_classes'))){
			$erreurs['css_classes'] = _T('sncore:regex_domclasses');
		}
	}
	
	if(count($erreurs)==0){
		$erreurs = formulaires_editer_objet_verifier('snpush',$id_snpush);
	}

	return $erreurs;

}

function formulaires_editer_snpush_traiter_dist($id_snpush='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	set_request('redirect', '');

	$retours = null;
	$autorisation = autoriser('modifier', 'snpush', session_get('id_auteur'));
	if ($autorisation === true) {
		/*if(_request('mel') !== null){
			$mel_sql = trim(strtolower(_request('mel'))); set_request('mel',$mel_sql);
		}
		if(_request('lien') !== null){
			$lien_sql = trim(strtolower(_request('lien'))); set_request('lien',$lien_sql);
		}*/
		if(_request('cible_article') !== null){
			$cible_id_sql = intval(_request('cible_article'));
			set_request('cible_id',$cible_id_sql);
		}
		if(_request('cible_auteur') !== null){
			$cible_id_sql = intval(_request('cible_auteur'));
			set_request('cible_id',$cible_id_sql);
		}
		if(_request('cible_rubrique') !== null){
			$cible_id_sql = intval(_request('cible_rubrique'));
			set_request('cible_id',$cible_id_sql);
		}
		$retours = formulaires_editer_objet_traiter('snpush',$id_snpush, $id_rubrique, $retour, $lier_trad, $config_fonc, $row, $hidden);
	}

	return $retours;

}
