.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
==============

Deny fileextensions
-------------------
You can global define fileextensions, which are not allowed to be shown, in the Extension-Manager. Default is “php, html” (comma seperated)

Template
--------

If you want to use another template, go to the Constant-editor and choose “PLUGIN.TX_PBFILELIST”. Input the path to your template, for example “fileadmin/template.html”.
You can orient yourself, if you look in the original template, which you can find here:
/typo3conf/ext/pb_filelist/Resources/Private/Templates/Filelist/index.html

Date format
-----------

If you want to change the date format, you can override the value, which is defined in locallang.xml by Typoscript.
You have to use the php-parameters (http://php.net/manual/en/function.date.php)

Example:
::

	plugin.tx_pbfilelist{
		_LOCAL_LANG.de{
			plugin.filelist.format.date = d.m.Y H:i:s
		}
	}

