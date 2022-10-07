<?php

TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Search', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.search.title', 'EXT:versatile_search/Resources/Public/Icons/Extension.svg', 'forms');
TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Form', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.form.title', 'EXT:versatile_search/Resources/Public/Icons/Extension.svg', 'forms');

$GLOBALS['TCA']['tt_content']['types']['versatilesearch_search'] = $GLOBALS['TCA']['tt_content']['types']['list'];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'pi_flexform', 'versatilesearch_search', 'after:list_type');
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('', 'FILE:EXT:versatile_search/Configuration/FlexForm/search.xml', 'versatilesearch_search');
$GLOBALS['TCA']['tt_content']['types']['versatilesearch_search']['showitem'] = preg_replace('/(?<=,)\s*(list_type|pages|recursive).*?,/', '', $GLOBALS['TCA']['tt_content']['types']['versatilesearch_search']['showitem']);

$GLOBALS['TCA']['tt_content']['types']['versatilesearch_form'] = $GLOBALS['TCA']['tt_content']['types']['list'];
$GLOBALS['TCA']['tt_content']['types']['versatilesearch_form']['showitem'] = preg_replace('/(?<=,)\s*(list_type|pages|recursive).*?,/', '', $GLOBALS['TCA']['tt_content']['types']['versatilesearch_form']['showitem']);
