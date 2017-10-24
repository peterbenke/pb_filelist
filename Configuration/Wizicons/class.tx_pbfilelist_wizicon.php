<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Peter Benke <peter.benke@nttdata.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */




/**
 * Class that adds the wizard icon.
 *
 * @author	Peter Benke <peter.benke@nttdata.com>
 * @package	TYPO3
 * @subpackage	tx_pbfilelist
 */
class tx_pbfilelist_wizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param	array		$wizardItems: The wizard items
	 * @return	Modified array with wizard items
	 */
	function proc($wizardItems)	{
		global $LANG;
	
		$LL = $this->includeLocalLang();
		$wizardItems['plugins_tx_pbfilelist'] = array(
			'icon'				=> t3lib_extMgm::extRelPath('pb_filelist').'Configuration/Wizicons/ce_wi_filelist.gif',
			'title'				=> $LANG->getLLL('filelist.plugin.title',$LL),
			'description'	=> $LANG->getLLL('filelist.plugin.description',$LL),
			'params'			=> '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=pbfilelist_filelist',

			'tt_content_defValues' => array(
				'CType' => 'list',
			),

		);
	
		return $wizardItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return	The array with language labels
	 */
	function includeLocalLang()	{

		$localizationParser = t3lib_div::makeInstance('t3lib_l10n_parser_Llxml');
		$LOCAL_LANG = $localizationParser->getParsedData(
			t3lib_extMgm::extPath('pb_filelist') . 'Resources/Private/Language/locallang_db.xml',
			$GLOBALS['LANG']->lang
		);		

		return $LOCAL_LANG;
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pb_filelist/Configuration/Wizicons/class.tx_pbfilelist_wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pb_filelist/Configuration/Wizicons/class.tx_pbfilelist_wizicon.php']);
}

?>
