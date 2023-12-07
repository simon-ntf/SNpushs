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
 * Bases de données du plugin SN Pushs
 *
 * @plugin snpushs
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function snpushs_declarer_tables_auxiliaires($tables){
	$tables['spip_snpushs_liens'] = [
		'field'=> [
			"id_snpush"			=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"			=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"				=> "varchar(25) DEFAULT '' NOT NULL",
			"sn_position"		=> "varchar(255) DEFAULT '' NOT NULL",
			"sn_priorite"		=> "int(3) DEFAULT '0' NOT NULL"
		],
		'key' => [
			"PRIMARY KEY" 		=> "id_snpush,id_objet,objet",
			"KEY id_snpush" 	=> "id_snpush",
			"KEY id_objet" 		=> "id_objet",
			"KEY objet" 		=> "objet",
			"KEY sn_position" 	=> "sn_position",
			"KEY sn_priorite" 	=> "sn_priorite"
		],
	];
	return $tables;
}

function snpushs_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['snschemas'] = 'snschemas';
	$interface['table_des_tables']['snpushs'] = 'snpushs';
	$interface['table_des_tables']['snpushs_liens'] = 'snpushs_liens'; 
	return $interface;
}

function snpushs_declarer_tables_objets_sql($tables) {
	$tables['spip_snpushs'] = [
		'principale' => "oui",
		'field'=> [
			"id_snpush" 		=> "bigint(21) NOT NULL",
			"id_document" 		=> "bigint(21) NOT NULL",
			"cible_id"	 		=> "bigint(21) NOT NULL",
			"date" 				=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"latitude"			=> "varchar(32) DEFAULT '' NOT NULL",
			"longitude"			=> "varchar(32) DEFAULT '' NOT NULL",
			"maj" 				=> "TIMESTAMP",
			"motscles"			=> "varchar(255) DEFAULT '' NOT NULL",
			"resume"			=> "varchar(255) DEFAULT '' NOT NULL",
			"statut"			=> "varchar(10) DEFAULT 'publie' NOT NULL",
			"texte"				=> "longtext DEFAULT '' NOT NULL",
			"titre"				=> "varchar(255) DEFAULT '' NOT NULL",
			"pushtype"			=> "varchar(10) DEFAULT 'libre' NOT NULL",
			"css_classes"		=> "varchar(255) DEFAULT '' NOT NULL",
		],
		'key' => [
			"PRIMARY KEY"		=> "id_snpush",
			"KEY id_document"	=> "id_document",
			"KEY statut"		=> "statut",
		],
		'champs_editables' 		=> ["id_document","cible_id","date","latitude","longitude","maj","motscles","resume","statut","texte","titre","pushtype","css_classes"],
		'titre'					=> "titre AS titre, '' AS lang",
		'date' 					=> "date",
		'info_aucun_objet' => 'snpushs:info_aucun_objet',
		'info_1_objet' => 'snpushs:info_1_objet',
		'info_nb_objets' => 'snpushs:info_nb_objets',
		'texte_ajouter'	=> 'snpushs:texte_ajouter',
		'texte_creer' => 'snpushs:texte_creer',
		'texte_creer_associer' => 'snpushs:texte_creer_associer',
		'texte_modifier' => 'snpushs:texte_modifier',
		'texte_objets' => 'snpushs:texte_objets',
		'texte_objet' => 'snpushs:texte_objet',
		'texte_retour' => 'icone_retour',
	];
	$tables['spip_snschemas'] = [
		'principale' => "oui",
		'field'=> [
			"id_snschema" 		=> "bigint(21) NOT NULL",
			"date" 				=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"maj" 				=> "TIMESTAMP",
			"statut"			=> "varchar(10) DEFAULT 'publie' NOT NULL",
			"titre"				=> "varchar(255) DEFAULT '' NOT NULL",
			"perimetre"			=> "varchar(10) DEFAULT '' NOT NULL",
		],
		'key' => [
			"PRIMARY KEY"		=> "id_snschema",
			"KEY statut"		=> "statut",
		],
		'champs_editables' 		=> ["date","maj","statut","titre","perimetre"],
		'titre'					=> "titre AS titre, '' AS lang",
		'date' 					=> "date",
		'info_aucun_objet' => 'snpushs:info_schemas_aucun_objet',
		'info_1_objet' => 'snpushs:info_schemas_1_objet',
		'info_nb_objets' => 'snpushs:info_schemas_nb_objets',
		'texte_ajouter'	=> 'snpushs:texte_schemas_ajouter',
		'texte_creer' => 'snpushs:texte_schemas_creer',
		'texte_creer_associer' => 'snpushs:texte_schemas_creer_associer',
		'texte_modifier' => 'snpushs:texte_schemas_modifier',
		'texte_objets' => 'snpushs:texte_schemas_objets',
		'texte_objet' => 'snpushs:texte_schemas_objet',
		'texte_retour' => 'icone_retour',
		'editable' => 'false',
	];
	return $tables;
}
