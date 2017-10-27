<?php
$EM_CONF[$_EXTKEY] = [
	'title' => 'Filelist',
	'description' => 'Generates a filelist from a directory. Sort the files by name, date or size.',
	'category' => 'fe',
	'author' => 'Peter Benke',
	'author_email' => 'info@typomotor.de',
	'author_company' => 'TYPO motor',
	'state' => 'stable',
	'uploadfolder' => 0,
	'clearCacheOnLoad' => 1,
	'version' => '3.0.0',
	'constraints' => [
		'depends' => [
			'typo3' => '7.6.0-8.7.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
