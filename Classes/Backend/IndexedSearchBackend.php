<?php

namespace WEBcoast\VersatileSearch\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
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
        $category = $this->getCurrentCategory();
        // Always use current language for searches
        $searchData['pointer'] = $page ? $page - 1 : 0;
        $searchData['sword'] = $searchString;
        $this->initializeSearchRepository($searchData);
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
            'types' => [],
            'results' => [],
            'pagination' => []
        ];
        if (count($indexConfigurations) > 0) {
            foreach ($indexConfigurations as $indexConfiguration) {
                if ($category === null || $category === (string)$indexConfiguration['uid']) {
                    $results = $this->searchRepository->doSearch($searchWords, $indexConfiguration['uid']);
                    if ($results['count'] > 0) {
                        $data['types'][] = [
                            'configuration' => $indexConfiguration,
                            'results' => $results['resultRows'],
                            'pagination' => $this->buildPagination($results['count'])
                        ];
                    }
                } else {
                    $data['types'][] = [
                        'configuration' => $indexConfiguration,
                        'results' => []
                    ];
                }
            }
        } else {
            $results = $this->searchRepository->doSearch($searchWords);
            $data['results'] = $results['resultRows'];
            $data['pagination'] = $this->buildPagination($results['count']);
        }

        return $data;
    }

    protected function initializeSearchRepository($searchData)
    {
        $searchData['languageUid'] = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0);
        $searchData['numberOfResults'] = $this->getPaginationItemsPerPage();
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

        return [
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
}
