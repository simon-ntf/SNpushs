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

if (!defined('_ECRIRE_INC_VERSION')) { return; }

/**
 * Renvoie la liste des blocs push associés à un objet. Si $sn_position est indiquee filtre sur une position.
 *
 * @param int 		$id_objet		ID de l'objet cible liable a des pushs
 * @param string 	$objet			Type de l'objet
 * @param string 	$sn_position	Emplacement de push ciblé
 * @return string 	$mode			Type de données en retour. Si "id" la fonction renvoie une liste des ID des pushs liés. Si autre renvoie toutes les données de liaison.
 * @return array 	ID des pushs liés / Données de liaison des pushs liés
*/
function snpushs_blocs_lies($id_objet,$objet,$sn_position='',$mode=''){
	$where = 'objet=' . sql_quote($objet) . ' AND id_objet=' . sql_quote($id_objet);
	$order = 'sn_priorite';
	$liens = sql_allfetsel('*', 'spip_snpushs_liens', $where, null, $order);
	$retour = [];
	$positions_arr = null;
	if($sn_position == ''){
		foreach($liens as $cle => $valeur){
			if($mode == 'id'){
				$retour[] = $valeur['id_snpush'];
			} else{
				$retour[] = $valeur;
			}
		}
	} else {
		foreach($liens as $cle => $valeur){
			$positions_arr = explode(',',$valeur['sn_position']);
			if(in_array($sn_position, $positions_arr)){
				if($mode == 'id'){
					$retour[] = $valeur['id_snpush'];
				} else{
					$retour[] = $valeur;
				}
			}
		}
	}
	return $retour;
}

/**
 * Transforme une liste de mots séparés par des virgules en liste de mots-clés avec lien et #
 *
 * @param string $liste Liste de termes séparés par des virgules
 * @return string $html Noisette HTML
*/
function sn_conv_motscles($liste){
	$mots = sn_explose($liste);
	$html = '';
	foreach($mots as $cle => $valeur){
		$rparam = str_replace(' ', '+', trim($valeur));
		$html .= '&#8239;<a class="sn-motcle" href="' . $GLOBALS['meta']['URL_SITE_SPIP'] . 'spip.php?page=recherche&recherche=' . $rparam . '">';
		if( strlen($GLOBALS['meta']['sn_icones_cle'] > 0) ){
			$html .= '<span class="fas fa-tag"></span>&nbsp;';
		}
		$html .= $valeur . '</a>';
	}
	return $html;
}

/**
 * Indique si un push peut occuper une position de push
 *
 * @param string 	$position	Emplacement de push ciblé
 * @param string 	$pushtype	Type de push
 * @param string 	$objet			Type de l'objet
 * @param int 		$id_objet		ID de l'objet cible liable a des pushs
 * @return string 	$mode			Type de données en retour. Si "id" la fonction renvoie une liste des ID des pushs liés. Si autre renvoie toutes les données de liaison.
 * @return (si non) string "" / (si oui) bool true
*/
function sn_push_position_autorisee($position,$pushtype,$objet,$id_objet){
	include_spip('inc/sn_const');
	$sn_const_PUSHS = sn_global_pushs_liaisons_objets();
	$sn_opt_push_positions = sn_const_options_liste('E','sn_position');
	$sn_opt_push_types = sn_const_options_liste('E','snpush_type');
	if((in_array($position,$sn_opt_push_positions)) && (in_array($pushtype,$sn_opt_push_types))){
		if($objet == 'snschema'){
			if($id_objet === '1'){
				$sn_const_PUSHS = sn_global_pushs_liaisons_accueil();
			}
		}
		if(isset($sn_const_PUSHS[$position])){
			return in_array($pushtype,$sn_const_PUSHS[$position]);
		}
	}
	return '';
}

/**
 * Renvoie les donnees des pushs assignes a un emplacement en fonction de la page et du type de page
 *
 * @param string 	$sn_position	Emplacement de push ciblé
 * @param string 	$objet (defaut)	Type de l'objet (et de la page qui appelle la fonction)
 * @param int 		$id_objet (0)	ID de l'objet cible liable a des pushs
 * @param int 		$id_parent (0)	ID rubrique parente de l'objet cible
 * @param int 		$id_secteur	(0)	ID secteur de l'objet cible
 * @return array	Données de liaison
*/
function sn_objet_liste_snpushs($sn_position,$objet='defaut',$id_objet=0,$id_parent=0,$id_secteur=0){
	include_spip('inc/sn_const');
	$nb = 0;
	$where = 'objet=' . sql_quote($objet) . ' AND id_objet=' . sql_quote($id_objet) . ' AND '. sql_quote($sn_position) .' IN sn_positions';
	$gambit_articles = $GLOBALS['meta']['sn_pushs_schema_articles'];
	$gambit_rubriques = $GLOBALS['meta']['sn_pushs_schema_rubriques'];
	$gambit_defaut = $GLOBALS['meta']['sn_pushs_schema_defaut'];
	$gambit_defaut_objets_tables = explode(',',$GLOBALS['meta']['sn_pushs_schema_defaut_objets']);
	$gambit_defaut_objets = [];
	foreach($gambit_defaut_objets_tables as $cle => $valeur){
		$terme = substr($valeur, 5);
		$terme = substr($terme, 0, -1);
		$gambit_defaut_objets[] = $terme;
	}
	/* Cas sommaire */
	$sn_liste_pushables = sn_global_objet_pushable();
	$sn_schema_pushables = sn_global_objet_pushable_schema();
	if($objet === 'accueil') {
		$objet = 'snschema';
		$id_objet = 1;
	}
	/* Cas objet éditorial */
	elseif(in_array($objet,$sn_liste_pushables)){
		if(in_array($objet,$gambit_defaut_objets)){
			if($gambit_defaut === 'on'){
				$objet = 'snschema';
				$id_objet = 2;
			}
		} else{
			if($objet == 'rubrique'){
				if($gambit_rubriques === 'sans'){
					$id_objet = 0;
				} elseif($gambit_rubriques === 'parent'){
					$id_objet = $id_secteur;
				}
			} elseif($objet == 'article') {
				if($gambit_articles === 'sans'){
					$id_objet = 0;
				}
				elseif($gambit_articles === 'parent'){
					$objet='rubrique';
					$id_objet = $id_parent;
					if($gambit_rubriques === 'parent'){
						$id_objet = $id_secteur;
					}
				}
			} else {
				$id_objet = $sn_schema_pushables[$objet];
				$objet = 'snschema';
			}
		}
	}
	/* Cas non objet editorial */
	else if($gambit_defaut === 'on'){
		$id_objet = 2;
		$objet = 'snschema';
	}
	return snpushs_blocs_lies($id_objet,$objet,$sn_position);
}
