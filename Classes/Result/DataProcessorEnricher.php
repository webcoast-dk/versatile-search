<?php

declare(strict_types=1);

namespace WEBcoast\VersatileSearch\Result;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class DataProcessorEnricher implements ResultEnricherInterface
{
    public function enrich(array $rawData, string $tableName, array $resultItem): array
    {
        $config = $this->getTypoScriptFrontendController()->tmpl->setup['plugin.']['tx_versatilesearch.']['result.'] ?? [];
        $dataProcessor = GeneralUtility::makeInstance(ContentDataProcessor::class);
        $cObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObject->start($rawData, $tableName);

        $resultItem = $dataProcessor->process($cObject, $config, $resultItem);

        return $resultItem;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
