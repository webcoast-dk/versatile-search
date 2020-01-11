<?php

namespace WEBcoast\VersatileSearch\Backend;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;
use TYPO3\CMS\IndexedSearch\Utility\IndexedSearchUtility;

class IndexedSearchBackend extends AbstractBackend
{
    /**
     * @var null|IndexSearchRepository
     */
    protected $searchRepository = null;

    public function initialize()
    {
        $this->searchRepository = GeneralUtility::makeInstance(IndexSearchRepository::class);
    }

    /**
     * @param string $searchString
     *
     * @return array
     */
    public function search($searchString)
    {
        $page = $this->getCurrentPage();
        // Always use current language for searches
        $searchData['pointer'] = $page ? $page - 1 : 0;
        $searchData['sword'] = $searchString;
        $this->initializeSearchRepository($searchData);
        $searchWords = IndexedSearchUtility::getExplodedSearchString($searchString, 'OR', []);
        $results = $this->searchRepository->doSearch($searchWords);

        return [
            'searchWords' => array_map(
                function ($item) {
                    return $item['sword'];
                },
                $searchWords),
            'results' => $results['resultRows'],
            'pagination' => $this->buildPagination($results['count'])
        ];
    }

    protected function initializeSearchRepository($searchData)
    {
        $searchData['languageUid'] = $searchData['languageUid'] = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0);
        $searchData['numberOfResults'] = $this->getPaginationItemsPerPage();
        $searchData['sortOrder'] = 'rank_flag';
        $this->searchRepository->initialize(
            [
                'searchSkipExtendToSubpagesChecking' => true,
                'exactCount' => true
            ],
            $searchData,
            [],
            (int)$GLOBALS['TSFE']->config['rootLine'][0]['uid']);
    }

    /**
     * Takes the raw result row and converts it into a standardized format to be used in the output.
     *
     * @param array $rawData
     *
     * @return array
     */
    public static function fetchResult($rawData)
    {
        $urlParameters = unserialize($rawData['cHashParams']);
        $urlParameters['L'] = $rawData['sys_language_uid'];
        ksort($urlParameters);
        $useCacheHash = false;
        if (isset($urlParameters['cHash'])) {
            $useCacheHash = true;
            unset($urlParameters['cHash']);
        }

        return [
            'title' => $rawData['item_title'],
            'description' => $rawData['item_description'],
            'pageId' => $rawData['page_id'],
            'urlParameters' => $urlParameters,
            'useCacheHash' => $useCacheHash,
            'created' => \DateTime::createFromFormat('U', $rawData['item_crdate']),
            'lastChanged' => \DateTime::createFromFormat('U', $rawData['item_mtime']),
            'category' => $rawData['freeIndexUid'],
            'recordId' => $rawData['recordUid']
        ];
    }
}
