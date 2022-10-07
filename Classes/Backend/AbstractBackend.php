<?php

namespace WEBcoast\VersatileSearch\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBcoast\VersatileSearch\Result\ResultEnricherInterface;

abstract class AbstractBackend
{
    protected $searchString = null;

    /**
     * The plugin settings
     *
     * @var array
     */
    protected $settings = [];

    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->initialize();
    }

    public abstract function initialize();

    /**
     * @param string     $searchString
     * @param int        $currentPage
     * @param null|mixed $category
     *
     * @return array
     */
    public abstract function search(string $searchString, int $currentPage, $category = null): array;

    /**
     * Return an array of words for search suggestions
     *
     * @param string $searchString
     * @param int    $maxItems
     * @param int    $languageUid
     *
     * @return array
     */
    public abstract function suggest(string $searchString, int $maxItems, int $languageUid): array;

    /**
     * Takes the raw result row and converts it into a standardized format to be used in the output.
     *
     * @param array $rawData
     *
     * @return array
     */
    public abstract static function fetchResult($rawData);

    protected static function enrichResultItem(array $rawResult, string $tableName, array $resultItem)
    {
        foreach($GLOBALS['TYPO3_CONF_VARS']['EXT']['versatile_search']['enrichResultItem'] ?? [] as $resultItemEnricherClass) {
            $enricher = GeneralUtility::makeInstance($resultItemEnricherClass);
            if (!$enricher instanceof ResultEnricherInterface) {
                throw new \RuntimeException(sprintf('%s must implement %s', get_class($enricher), ResultEnricherInterface::class));
            }

            $resultItem = $enricher->enrich($rawResult, $tableName, $resultItem);
        }

        return $resultItem;
    }
}
