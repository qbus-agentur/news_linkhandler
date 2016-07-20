<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['news'] =
    \Qbus\NewsLinkhandler\Hooks\TypolinkLinkHandler::class;
