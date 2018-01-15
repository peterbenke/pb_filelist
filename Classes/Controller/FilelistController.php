<?php
namespace PeterBenke\PbFilelist\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2017 Peter Benke <info@typomotor.de>
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
 * FilelistController
 *
 */
class FilelistController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 */
	var $resourceFactory;

	/**
	 * Extension configuration
	 *
	 * @var	array
	 */	
	private $extConf = [];
	
	/**
	 * DenyFileExtensions () defined in extension configuration
	 *
	 * @var	array
	 */	
	private $denyFileExtensions = [];

	/**
	 * Only use file extensions (defined in flexform)
	 *
	 * @var array
	 */
	private $onlyUseFileExtensions = [];
	
	
	/*
	 * Initialize
	 * 
	 * @param none
	 * @return void
	 */
	public function initializeAction(){

		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pb_filelist']);

		// Extension configuration
		if(!empty(trim($this->extConf['denyFileExtensions']))){
			$this->denyFileExtensions = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', strtolower($this->extConf['denyFileExtensions']));
		}

		// Flexform
		if(!empty(trim($this->settings['fileextensions']))){
			$this->onlyUseFileExtensions = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', strtolower($this->settings['fileextensions']));
		}

	}

	/**
	 * @return void
	 */
	public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view){

		$this->resourceFactory = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

	}
	
	/**
	 * action index
	 * Shows the files
	 *
	 * @param none
	 * @return void
	 */
	public function indexAction() {

		if(empty($this->settings['folder'])){
			return;
		}

		$folderObj = $this->resourceFactory->retrieveFileOrFolderObject($this->settings['folder']);
		$filesOrig = $folderObj->getFiles();

		$files = [];
		$count = 0;

		/** @var \TYPO3\CMS\Core\Resource\File $file */
		foreach($filesOrig as $file){

			// Check, if file extension is allowed
			$extension = strtolower($file->getExtension());

			$fileExtensionAllowed = true;

			if(in_array($extension, $this->denyFileExtensions)){
				$fileExtensionAllowed = false;
			}

			if(!in_array($extension, $this->onlyUseFileExtensions) && !empty($this->onlyUseFileExtensions)){
				$fileExtensionAllowed = false;
			}

			if($fileExtensionAllowed){

				$files[$count]['fileObject'] = $file;
				$files[$count]['dateFormatted'] = date(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_pbfilelist.format.date', $this->extensionName), $file->getProperty('modification_date'));
				$files[$count]['filename'] = $file->getName();
				$files[$count]['filesize'] = $this->byteSize($file->getSize());

				$files[$count]['sortby_path'] = strtolower($file->getName());
				$files[$count]['sortby_date'] = $file->getProperty('modification_date');
				$files[$count]['sortby_size'] = $file->getSize();

				if(intval($this->settings['cutfilename']) > 0){
					$files[$count]['filename'] = $this->cutFilename($files[$count]['filename'], intval($this->settings['cutfilename']));
				}

				$count++;

			}

		}

		// Now get the new, sorted array
		$files = $this->getSortedArray($files, 'sortby_' . $this->settings['sortBy'], $this->settings['order']);
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
	private function getSortedArray($array, $sortByKey = 'sortby_path', $order = 'asc'){

		$newArray = [];

		for($i=0;$i<count($array);$i++){
			
			$key = $array[$i][$sortByKey] . $i; // So we never have to same keys
			$newArray[$key]['fileObject'] = $array[$i]['fileObject'];
			$newArray[$key]['dateFormatted'] = $array[$i]['dateFormatted'];
			$newArray[$key]['filename'] = $array[$i]['filename'];
			$newArray[$key]['filesize'] = $array[$i]['filesize'];

		}

		if($order == 'desc'){
			krsort($newArray);
		} else {
			ksort($newArray);
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
	 * Returns a formatted file size
	 * @param string not formatted file size
	 * @return string formatted file size
	 */
	private function byteSize($bytes){

		if (!is_int($bytes) || $bytes < 0){
			return false;
		}

		$map = [
			// 'GB' => [1073741824, 1],
			'GB' => [1024000000, 1],
			// 'MB' => [1048576, 2],
			'MB' => [1024000, 2],
			'KB' => [1024, 2],
			'Bytes' => [1, 0],
		];

		foreach($map as $k => $v){
			if ($bytes >= $v[0]){
				break;
			}
		}

		# $f = number_format($bytes / $v[0], $v[1],',','.');
		// $f = number_format($bytes / $v[0], 1,',','.');
		// print_r($this->settings);
		$f = number_format(
			$bytes / $v[0],
			1,
			$this->settings['formatFileSize']['decimalPoint'],
			$this->settings['formatFileSize']['thousandsSeparator']
		);

		if ($bytes < 2){
			$k = 'Byte';
		}

		return sprintf ('%s %s',$f, $k);
	}	

}