<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Filelist',
	'LLL:EXT:pb_filelist/Resources/Private/Language/locallang_db.xml:filelist.plugin.title'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Filelist from a directory');

// Flexform
$pluginSignature	= 'pbfilelist_filelist';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue(
	$pluginSignature,
	'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform-filelist.xml'
);

// Plugin
if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_pbfilelist_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Wizicons/class.tx_pbfilelist_wizicon.php';
}


?>