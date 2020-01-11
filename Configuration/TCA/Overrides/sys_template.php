<?php

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'versatile_search',
    'Configuration/TypoScript/',
    TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Localization\LanguageService::class)->sL('LLL:EXT:versatile_search/Resources/Private/Language/backend.xlf:plugin.typoscript'));
