<?php

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['versatilesearch_search'] = 'pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['versatilesearch_form'] = 'pages,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['versatilesearch_search'] = 'pi_flexform';
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('versatilesearch_search', 'FILE:EXT:versatile_search/Configuration/FlexForm/search.xml');

TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Search', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.search.title');
TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Form', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.form.title');
