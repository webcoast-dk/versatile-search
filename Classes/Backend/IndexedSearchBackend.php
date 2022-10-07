<?php

namespace WEBcoast\VersatileSearch\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;
use TYPO3\CMS\IndexedSearch\Utility\IndexedSearchUtility;
use WEBcoast\VersatileSearch\Pagination\IndexedSearchPaginator;

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
     * @param int    $currentPage
     * @param null   $category
     *
     * @return array
     */
    public function search(string $searchString, int $currentPage = 1, $category = null): array
    {
        // Always use current language for searches
        $searchData['pointer'] = $currentPage ? $currentPage - 1 : 0;
        $searchData['sword'] = $searchString;
        $itemsPerPage = (int) ($this->settings['pagination']['itemsPerPage'] ?? 10);
        $this->initializeSearchRepository($searchData, $itemsPerPage);
        $searchWords = IndexedSearchUtility::getExplodedSearchString($searchString, 'OR', []);
        $indexConfigurations = [];
        foreach (GeneralUtility::trimExplode(',', $this->settings['indexConfigurations'], true) as $indexConfigurationId) {
            $indexConfiguration = BackendUtility::getRecord('index_config', $indexConfigurationId);
            if ($indexConfiguration && !$indexConfiguration['hidden']) {
                $indexConfigurations[] = $indexConfiguration;
            }
        }

        $data = [
            'searchWords' => array_map(
                function ($item) {
                    return $item['sword'];
                },
                $searchWords),
            'categories' => [],
            'results' => [],
            'paginator' => []
        ];

        if (count($indexConfigurations) > 0) {
            foreach ($indexConfigurations as $indexConfiguration) {
                if ($category === null || $category === (string)$indexConfiguration['uid']) {
                    $results = $this->searchRepository->doSearch($searchWords, $indexConfiguration['uid']);
                    if ($results['count'] > 0) {
                        $data['categories'][] = [
                            'configuration' => $indexConfiguration,
                            'results' => $results['resultRows'],
                            'paginator' => GeneralUtility::makeInstance(IndexedSearchPaginator::class, $results['resultRows'], $results['count'], $itemsPerPage, $currentPage),
                        ];
                    }
                } else {
                    $data['categories'][] = [
                        'configuration' => $indexConfiguration,
                        'results' => [],
                        'paginator' => null,
                    ];
                }
            }
        } else {
            $results = $this->searchRepository->doSearch($searchWords);
            $data['results'] = $results['resultRows'];
            $data['paginator'] = GeneralUtility::makeInstance(IndexedSearchPaginator::class, $results['resultRows'], $results['count'], $itemsPerPage, $currentPage);
        }

        return $data;
    }

    public function suggest(string $searchString, int $maxItems, int $languageUid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('index_words');
        $queryBuilder
            ->select('w.baseword')
            ->from('index_words', 'w')
            ->join('w', 'index_rel', 'r', 'w.wid=r.wid')
            ->join('r', 'index_phash', 'p', 'r.phash=p.phash')
            ->where(
                $queryBuilder->expr()->like('w.baseword', $queryBuilder->createNamedParameter($searchString . '%')),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter($languageUid, \PDO::PARAM_INT))
            )
            ->groupBy('w.baseword')
            ->setMaxResults($maxItems)
            ->orderBy('w.baseword', 'asc');

        $result = $queryBuilder->executeQuery();
        $words = $result->fetchFirstColumn();
        $result->free();

        return $words;
    }

    protected function initializeSearchRepository($searchData, int $itemsPerPage)
    {
        $searchData['languageUid'] = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0);
        $searchData['numberOfResults'] = $itemsPerPage;
        $searchData['sortOrder'] = 'rank_flag';
        $searchData['mediaType'] = '-1'; // Search for everything
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
        $urlParameters = !empty($rawData['static_page_arguments']) ? json_decode($rawData['static_page_arguments'], true) : unserialize($rawData['cHashParams']);
        $urlParameters['L'] = $rawData['sys_language_uid'];
        ksort($urlParameters);
        $useCacheHash = false;
        if (isset($urlParameters['cHash'])) {
            $useCacheHash = true;
            unset($urlParameters['cHash']);
        }

        $result = [
            'title' => $rawData['item_title'],
            'type' => self::getItemType($rawData),
            'description' => $rawData['item_description'],
            'pageId' => $rawData['page_id'],
            'urlParameters' => $urlParameters,
            'useCacheHash' => $useCacheHash,
            'created' => \DateTime::createFromFormat('U', $rawData['item_crdate']),
            'lastChanged' => \DateTime::createFromFormat('U', $rawData['item_mtime']),
            'category' => $rawData['freeIndexUid'],
            'recordId' => $rawData['recordUid'],
            'fileName' => $rawData['data_filename']
        ];

        $rawRecord = [];
        $tableName = '';
        if ($rawData['page_id'] > 0) {
            $rawRecord = self::getTypoScriptFrontendController()->sys_page->getPage($rawData['page_id']);
            $tableName = 'pages';
        } elseif ($rawData['recordUid'] > 0) {
            $tableName = self::getTypoScriptFrontendController()->tmpl->setup['plugin.tx_versatilesearch.']['result.']['tableMapping.'][$rawData['freeIndexUid']] ?? null;
            if ($tableName) {
                $rawRecord = self::getTypoScriptFrontendController()->sys_page->checkRecord($tableName, $rawData['recordUid']);
            }
        }

        return self::enrichResultItem($rawRecord, $tableName, $result);
    }

    protected static function getItemType($rawData)
    {
        if (isset($rawData['data_filename']) && !empty(trim($rawData['data_filename']))) {
            return 'file';
        }
        if (isset($rawData['page_id']) && $rawData['page_id'] > 0) {
            return 'page';
        }

        return '';
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
