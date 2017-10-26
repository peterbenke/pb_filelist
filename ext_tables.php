<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'PeterBenke.' . $_EXTKEY,
	'Filelist',
	'LLL:EXT:pb_filelist/Resources/Private/Language/locallang_db.xlf:tx_pbfilelist.plugin.title'
);

/**
 * Typoscript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Filelist from a directory');

/**
 * Flexform
 */
$pluginSignature	= 'pbfilelist_filelist';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	$pluginSignature,
	'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform-filelist.xml'
);
