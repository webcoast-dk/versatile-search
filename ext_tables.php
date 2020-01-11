<?php


if (TYPO3_MODE === 'BE') {
    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Search', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.search.title');
    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('WEBcoast.VersatileSearch', 'Form', 'LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.form.title');
}
