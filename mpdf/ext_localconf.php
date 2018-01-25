<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
          'PV.'.$extKey,
            'pi1',
            [
                'Sample' => 'pdfGenerator'
                
            ],
            // non-cacheable actions
            [
                
                'Sample' => 'pdfGenerator'
            ]
        );
        


    },
    $_EXTKEY
);
