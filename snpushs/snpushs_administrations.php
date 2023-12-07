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

function snpushs_upgrade($nom_meta_base_version, $version_cible){
	$maj = [];
	$maj['create'] = [
		['maj_tables', [
			"spip_snschemas",
			"spip_snpushs",
			"spip_snpushs_liens",
		]],
		['sql_insertq', 'spip_snschemas', ['titre'=>_T('snpushs:snschema_titre_accueil'),'date'=>date("Y-m-d H:i:s"),'statut'=>'publie','perimetre'=>'sommaire']],
		['sql_insertq', 'spip_snschemas', ['titre'=>_T('snpushs:snschema_titre_defaut'),'date'=>date("Y-m-d H:i:s"),'statut'=>'publie','perimetre'=>'defaut']],
		['sql_insertq', 'spip_snschemas', ['titre'=>_T('snpushs:snschema_titre_auteurs'),'date'=>date("Y-m-d H:i:s"),'statut'=>'publie','perimetre'=>'auteurs']],
		['sql_insertq', 'spip_snschemas', ['titre'=>_T('snpushs:snschema_titre_mots'),'date'=>date("Y-m-d H:i:s"),'statut'=>'publie','perimetre'=>'mots']],
		['sql_insertq', 'spip_snschemas', ['titre'=>_T('snpushs:snschema_titre_secteurs'),'date'=>date("Y-m-d H:i:s"),'statut'=>'publie','perimetre'=>'secteurs']],
	];
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}
function snpushs_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_snpushs_liens");
	sql_drop_table("spip_snpushs");
	sql_drop_table("spip_snschemas");
	
	effacer_meta($nom_meta_base_version);
}
