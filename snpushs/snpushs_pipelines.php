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

if (!function_exists('snpushs_configurer_liste_metas')){ function snpushs_configurer_liste_metas($metas){
	$metas['sn_pushs_alertes_globales'] = 'on'; // on|off
	$metas['sn_pushs_schema_defaut'] = ''; // on|off
	$metas['sn_pushs_schema_defaut_objets'] = ''; // tables objets SPIP
	$metas['sn_pushs_schema_articles'] = 'local'; // local | parent | sans
	$metas['sn_pushs_schema_rubriques'] = 'local'; // local | parent | sans
	$metas['sn_pushs_autoriser_redacteurs'] = 'on'; // on|off
	return $metas;
}}

if (!function_exists('snpushs_affiche_gauche')){ function snpushs_affiche_gauche($flux){
	$page_exec = $flux['args']['exec'];
	if( ($page_exec == 'snpushs') || ($page_exec == 'snschemas') ){
		if($page_exec == 'snschemas'){
			$flux['data'] .= '<div class="box info"><p class="sn-prive-demarge sn-prive-padding-16">' . _T('snpushs:colonne_snschemas_texte') . '</p></div>';
		} elseif($page_exec == 'snpushs'){
			$flux['data'] .= '<div class="box info"><p class="sn-prive-demarge sn-prive-padding-16">' . _T('snpushs:colonne_snpushs_texte') . '</p></div>';
		}
	}
	return $flux;
}}

if (!function_exists('snpushs_affiche_milieu')){ function snpushs_affiche_milieu($flux){
	$tables_pushables = [
		'article','rubrique','snschema',
	];
	if ($e = trouver_objet_exec($flux['args']['exec']) AND $e['edition'] !== true) {
		$affmilieu = '';
		$obj_tables = [ 'article' => 'spip_articles', 'rubrique' => 'spip_rubriques', 'snschema' => 'spip_snschemas'];
		if(in_array($e['type'],$tables_pushables)){
			$affmilieu .= recuperer_fond('prive/objets/editer/liens', [
				'table_source'=>'snpushs',
				'objet'=>$e['type'],
				'id_objet'=>$flux['args'][$e['id_table_objet']],
				'editable'=>autoriser('associer'.$e['type'], $e['type'], $flux['args'][$e['id_table_objet']])? 'oui' : 'non'
			]);
			if($p=strpos($flux['data'],"<!--affiche_milieu-->")){
				$flux['data'] = substr_replace($flux['data'],$affmilieu,$p,0);
			}
		}
	}
	return $flux;
}}

if (!function_exists('snpushs_post_edition_lien')){ function snpushs_post_edition_lien($flux) {
	$objet = $flux['args']['objet']; // Type objet parent
	$id_objet = $flux['args']['id_objet']; // Id objet parent
	$objet_source = $flux['args']['objet_source']; // Type objet enfant
	$id_objet_source = $flux['args']['id_objet_source']; // Id objet enfant
	$tables_ordonnables = [
		'snpush' => 'spip_snpushs_liens',
	];
	// Ordonnancement
	if ($table_objet = array_search($flux['args']['table_lien'],$tables_ordonnables)){
		$req = sql_select('*', $flux['args']['table_lien'], 'objet=' . sql_quote($objet) . ' AND id_objet=' . sql_quote($id_objet));
		$nb_liens = sql_count($req);
		if ($objet_source == 'snpush'){
			// Affiliation : ordre = nb objs -1
			if($flux['args']['action'] == 'insert' && $nb_liens > 0){
				$nouvelle_position = $nb_liens - 1;
				sql_updateq($flux['args']['table_lien'], ['sn_priorite'=>sql_quote($nouvelle_position)], 'id_' . $table_objet . ' = ' . sql_quote($id_objet_source) . ' AND objet=' . sql_quote($objet) . ' AND id_objet = ' . sql_quote($id_objet));
			}
			// Desaffiliation : renumerote les liens restant
			elseif($flux['args']['action'] == 'delete' && $nb_liens > 0){
				$switch_position = false;
				// On stocke les Ids par position
				$liens_datas = [];
				while($res = sql_fetch($req)){
					$liens_datas[$res['sn_priorite']] = $res['id_' . $table_objet];
				}
				for($i = 0 ; $i < $nb_liens ; $i++){
					// Lorsque la position du lien supprime est atteinte, on recale tous ceux positionnes apres
					if(!isset($liens_datas[$i])){
						$switch_position = true;
					}
					if($switch_position == true){
						if(isset($liens_datas[$i+1])){
							sql_updateq($flux['args']['table_lien'], ['sn_priorite'=>sql_quote($i)], 'id_' . $table_objet . ' = ' . sql_quote($liens_datas[$i+1]) . ' AND objet=' . sql_quote($objet) . ' AND id_objet = ' . sql_quote($id_objet));
						}
					}
				}
			}
		}
	}
	return $flux;
}}
