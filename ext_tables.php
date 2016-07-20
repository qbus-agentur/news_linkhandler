<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        TCEMAIN.linkHandler.news {
            handler = Qbus\NewsLinkhandler\LinkHandler\NewsLinkHandler
            label = News
            displayAfter = page
            scanAfter = page
	}
    ');
}
