<?php

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Search', [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'search'], [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'search']);
TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Form', [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'form']);

$searchBackend = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('versatile_search', 'search/backend');
switch ($searchBackend) {
    case 'indexed_search':
        WEBcoast\VersatileSearch\Utility\BackendUtility::setSearchBackend(WEBcoast\VersatileSearch\Backend\IndexedSearchBackend::class);
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['enrichResultItem'][1660903792] = \WEBcoast\VersatileSearch\Result\DataProcessorEnricher::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['enrichResultItem'][1660909405] = \WEBcoast\VersatileSearch\Result\TypoScriptEnricher::class;
