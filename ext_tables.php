<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'powermail_from_override',
    'Configuration/TypoScript',
    'Powermail Functionality: Static From'
);