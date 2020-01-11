<?php

namespace WEBcoast\VersatileSearch\Utility;


use TYPO3\CMS\Core\SingletonInterface;

class BackendUtility implements SingletonInterface
{
    /**
     * @param string $class            The class name for the queue and crawl methods.
     */
    public static function setSearchBackend($class)
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['backend'] = $class;
    }

    public static function getSearchBackend()
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['backend'])) {
            throw new \RuntimeException('No search backend was set. Please choose one in `versatile_search` extension configuration or register one in another extension');
        }

        return $GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['backend'];
    }
}
