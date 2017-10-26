<?php
defined('TYPO3_MODE') or die();

/**
 * Configure plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(

	'PeterBenke.' . $_EXTKEY,

	'Filelist',

	[
		'Filelist'	=> 'index'
	],

	// non-cacheable actions
	[
		'Filelist'	=> 'index'
	]

);

/**
 * Register icons
 */
if (TYPO3_MODE == 'BE') {
	$pageType = 'pbfilelist'; // a maximum of 10 characters
	$icons = [
		'ext-pbfilelist-wizard-icon' => 'plugin_wizard.svg'
	];
	/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
	$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
	foreach ($icons as $identifier => $filename) {
		$iconRegistry->registerIcon(
			$identifier,
			$iconRegistry->detectIconProvider($filename),
			['source' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/' . $filename]
		);
	}
}

/**
 * Page TsConfig
 */
// -----------------------------------------------------------------------------------------------------------------------------------------
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pb_filelist/Configuration/TSConfig/ContentElementWizard.ts">');
