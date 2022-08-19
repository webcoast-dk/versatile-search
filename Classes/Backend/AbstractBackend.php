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
     * @param string $searchString
     *
     * @return array
     */
    public abstract function search($searchString);

    /**
     * Returns the current page as integer. This is the value of the page parameter or 1, if the parameter is not set.
     *
     * @return int
     */
    protected function getCurrentPage()
    {
        return (int)(GeneralUtility::_GP($this->settings['parameters']['page'] ?? 'page') ?? 1);
    }

    protected function getCurrentCategory() {
        return GeneralUtility::_GP($this->settings['parameters']['category'] ?? 'c') ?? null;
    }

    protected function getPaginationItemsPerPage()
    {
        return $this->settings['pagination']['itemsPerPage'] ?? 10;
    }

    protected function getPaginationMaximumNumberOfLinks()
    {
        return $this->settings['pagination']['maximumNumberOfLinks'] ?? 7;
    }

    protected function buildPagination($totalResultNumber)
    {
        $numberOfPages = $this->getPaginationItemsPerPage() > 0 ? ceil($totalResultNumber / $this->getPaginationItemsPerPage()) : 0;
        $currentPage = $this->getCurrentPage();
        [$displayRangeStart, $displayRangeEnd] = $this->calculateDisplayRange($numberOfPages, $this->getPaginationMaximumNumberOfLinks(), $currentPage);
        $pages = [];
        for ($i = $displayRangeStart; $i <= $displayRangeEnd; $i++) {
            $pages[] = ['number' => $i, 'isCurrent' => $i === $currentPage];
        }
        $pagination = [
            'pages' => $pages,
            'current' => $currentPage,
            'numberOfPages' => $numberOfPages,
            'displayRangeStart' => $displayRangeStart,
            'displayRangeEnd' => $displayRangeEnd,
            'hasLessPages' => $displayRangeStart > 2,
            'hasMorePages' => $displayRangeEnd + 1 < $numberOfPages
        ];
        if ($currentPage < $numberOfPages) {
            $pagination['nextPage'] = $currentPage + 1;
        }
        if ($currentPage > 1) {
            $pagination['previousPage'] = $currentPage - 1;
        }

        return $pagination;
    }

    protected function calculateDisplayRange($numberOfPages, $maximumNumberOfLinks, $currentPage)
    {
        if ($maximumNumberOfLinks > $numberOfPages) {
            $maximumNumberOfLinks = $numberOfPages;
        }
        $delta = floor($maximumNumberOfLinks / 2);
        $displayRangeStart = $currentPage - $delta;
        $displayRangeEnd = $currentPage + $delta - ($maximumNumberOfLinks % 2 === 0 ? 1 : 0);
        if ($displayRangeStart < 1) {
            $displayRangeEnd -= $displayRangeStart - 1;
        }
        if ($displayRangeEnd > $numberOfPages) {
            $displayRangeStart -= $displayRangeEnd - $numberOfPages;
        }
        $displayRangeStart = (int)max($displayRangeStart, 1);
        $displayRangeEnd = (int)min($displayRangeEnd, $numberOfPages);

        return [$displayRangeStart, $displayRangeEnd];
    }

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
