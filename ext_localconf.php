<?php

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Search', [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'search'], [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'search'], \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT);
TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Form', [\WEBcoast\VersatileSearch\Controller\SearchController::class => 'form'], [], \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT);

$searchBackend = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('versatile_search', 'search/backend');
switch ($searchBackend) {
    case 'indexed_search':
        WEBcoast\VersatileSearch\Utility\BackendUtility::setSearchBackend(WEBcoast\VersatileSearch\Backend\IndexedSearchBackend::class);
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['enrichResultItem'][1660903792] = \WEBcoast\VersatileSearch\Result\DataProcessorEnricher::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['enrichResultItem'][1660909405] = \WEBcoast\VersatileSearch\Result\TypoScriptEnricher::class;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import \'EXT:versatile_search/Configuration/TSConfig/Page/setup.tsconfig\'');

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)->registerIcon(
    'versatile-search-plugin',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    [
        'source' => 'EXT:versatile_search/Resources/Public/Icons/Extension.svg'
    ]
);
