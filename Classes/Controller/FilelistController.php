<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Peter Benke <peter.benke@nttdata.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @package pb_filelist
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_PbFilelist_Controller_FilelistController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Extension configuration
	 *
	 * @var	array
	 */	
	private $extConf = array();
	
	/**
	 * DenyFileExtensions
	 *
	 * @var	array
	 */	
	private $denyFileExtensions = array();	
	
	
	/*
	 * Initialize
	 * 
	 * @param none
	 * @return void
	 */
	public function initializeAction(){

		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pb_filelist']);
		$this->denyFileExtensions = explode(',', $this->extConf['denyFileExtensions']);
		
		for($i=0;$i<count($this->denyFileExtensions);$i++){
			$this->denyFileExtensions[$i] = trim($this->denyFileExtensions[$i]);
		}
	
	}
	
	
	
	/**
	 * action index
	 * Shows the files
	 *
	 * @param none
	 * @return void
	 */
	public function indexAction() {

		if(!empty($this->settings['templateFile'])){
			$this->view->setTemplatePathAndFilename($this->settings['templateFile']);
		}
		
		$folder = PATH_site . $this->settings['folder'];

		if (
				!is_dir($folder)
				||
				PATH_site == ''
				||
				empty($this->settings['folder'])
				||
				preg_match('/^typo3/', $this->settings['folder'])
		){
			return;
		}

		// Get the filelist
		if(!empty($this->settings['fileextensions'])){
			$this->settings['fileextensions'] = trim($this->settings['fileextensions']);
			$filelist = glob($folder . '*.{' . $this->settings['fileextensions'] . '}', GLOB_BRACE);
		} else {
			$filelist = glob($folder . '*.*');
		}
		
		$files = array();

		// Get filetime data
		for($i=0;$i<count($filelist);$i++){

			$deny = false;
			for($j=0;$j<count($this->denyFileExtensions);$j++){
				if(preg_match('/\.' . $this->denyFileExtensions[$j] . '$/', $filelist[$i])){
					$deny = true;
					break;
				}
			}
			
			if(!$deny){
				
				$files[$i]['path']					= str_replace(PATH_site, '', $filelist[$i]);
				$files[$i]['filename']			= str_replace('/', '', strrchr($filelist[$i], '/'));
				$files[$i]['title']					= $files[$i]['filename'];
				$files[$i]['extension']			= str_replace('.', '', strrchr($filelist[$i], '.'));
				$files[$i]['date']					= filemtime($filelist[$i]);
				$files[$i]['dateFormatted']	= date(Tx_Extbase_Utility_Localization::translate('plugin.filelist.format.date', $this->extensionName), filemtime($filelist[$i]));
				$files[$i]['filesize']			= $this->byteSize(filesize($filelist[$i]));

				// Cut the filename
				if(intval($this->settings['cutfilename']) > 0){
					$files[$i]['filename'] = $this->cutFilename($files[$i]['filename'], intval($this->settings['cutfilename']));
				}
				
				// Check absRefPrefix
				if(!empty($GLOBALS['TSFE']->config['config']['absRefPrefix'])){
					$files[$i]['path'] = $GLOBALS['TSFE']->config['config']['absRefPrefix'] . $files[$i]['path'];
				}

			}

		}

		// Reset the Array-Index (0,1,2,...)
		$files = array_merge($files);

		// Now get the new, sorted array
		$files = $this->getSortedArray($files, $this->settings['sortBy'], $this->settings['order']);

		$this->view->assign('numberOfFiles', count($files));
		$this->view->assign('files', $files);

	}
	
	
	/*
	 * Returns a sorted array by filepath/date asc/desc
	 * @param array old array
	 * @param string sortByKey (path/date)
	 * @param string order (asc/desc)
	 * @return array
	 */
	private function getSortedArray($array, $sortByKey = 'path', $order = 'asc'){
		
		$newArray = array();

		for($i=0;$i<count($array);$i++){
			
			$key = $array[$i][$sortByKey] . '-' . $i;
			
			$newArray[$key]['path']						= $array[$i]['path'];
			$newArray[$key]['filename']				= $array[$i]['filename'];
			$newArray[$key]['title']					= $array[$i]['title'];
			$newArray[$key]['extension']			= $array[$i]['extension'];
			$newArray[$key]['date']						= $array[$i]['date'];
			$newArray[$key]['dateFormatted']	= $array[$i]['dateFormatted'];
			$newArray[$key]['filesize']				= $array[$i]['filesize'];
			
		}

		if($order == 'desc'){
			rsort($newArray);
		} else {
			sort($newArray);
		}
		
		return $newArray;
		
	}
	
	
	/*
	 * Cuts a filename like filename...pdf
	 * @param string filename
	 * @param int number of letters
	 * @return string cutted filename
	 */
	private function cutFilename($filename, $cutnumber){

		$ext = str_replace('.', '', strrchr($filename, '.'));
		$fileNameWithoutExt = preg_replace('/\.' . $ext . '$/', '', $filename);

		if(strlen($fileNameWithoutExt) <= $cutnumber){
			return $filename;
		}
		
		$filename = substr($fileNameWithoutExt, 0, $cutnumber) . '...' . $ext;
		return $filename;
		
	}
	
	/*
	 * Returns a formatted filesize
	 * @param string unformatted filesize
	 * @return string formatted filesize
	 */
	private function byteSize($bytes){

		if (!is_int($bytes) || $bytes < 0){
			return false;
		}

		$map = array(
			// 'GB' => array(1073741824, 1),
			'GB' => array(1024000000, 1),
			// 'MB' => array(1048576, 2),
			'MB' => array(1024000, 2),
			'KB' => array(1024, 2),
			'Bytes' => array(1, 0),
		);

		foreach($map as $k => $v){
			if ($bytes >= $v[0]){
				break;
			}
		}

		# $f = number_format($bytes / $v[0], $v[1],',','.');
		$f = number_format($bytes / $v[0], 1,',','.');

		if ($bytes < 2){
			$k = 'Byte';
		}

		return sprintf ('%s %s',$f, $k);
	}	
	

}
?>
