<?php

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Search', ['Search' => 'search'], ['Search' => 'search']);
TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('WEBcoast.VersatileSearch', 'Form', ['Search' => 'form']);

$searchBackend = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('versatile_search', 'search/backend');
switch ($searchBackend) {
    case 'indexed_search':
        WEBcoast\VersatileSearch\Utility\BackendUtility::setSearchBackend(WEBcoast\VersatileSearch\Backend\IndexedSearchBackend::class);
}
