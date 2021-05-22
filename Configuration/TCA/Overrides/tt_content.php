<?php

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['versatilesearch_search'] = 'pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['versatilesearch_search'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['versatilesearch_form'] = 'pages,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('versatilesearch_search', 'FILE:EXT:versatile_search/Configuration/FlexForm/search.xml');
