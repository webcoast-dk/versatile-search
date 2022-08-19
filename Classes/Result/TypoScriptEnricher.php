<?php

declare(strict_types=1);

namespace WEBcoast\VersatileSearch\Result;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class TypoScriptEnricher implements ResultEnricherInterface
{
    public function enrich(array $rawData, string $tableName, array $resultItem): array
    {
        $config = $this->getTypoScriptFrontendController()->tmpl->setup['plugin.']['tx_versatilesearch.']['result.']['variables.'] ?? [];

        $cObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObject->start($rawData, $tableName);
        foreach($config as $name => $conf) {
            if (str_ends_with($name, '.')) {
                $name = substr($name, 0, -1);
                $resultItem[$name] = $cObject->stdWrap($config[$name] ?? '', $config[$name . '.'] ?? []);
            } elseif (!isset($config[$name . '.'])) {
                $resultItem[$name] = $conf;
            }
        }

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
