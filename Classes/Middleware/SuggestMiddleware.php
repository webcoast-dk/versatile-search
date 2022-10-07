<?php

declare(strict_types=1);

namespace WEBcoast\VersatileSearch\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBcoast\VersatileSearch\Backend\AbstractBackend;
use WEBcoast\VersatileSearch\Utility\BackendUtility;

class SuggestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/search-auto-complete') {
            $searchString = $request->getQueryParams()['q'];
            try {
                $minCharacters = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('versatile_search', 'suggest.minCharacters') ?? 3;
            } catch (ExtensionConfigurationPathDoesNotExistException $exception) {
                $minCharacters = 3;
            }

            if (empty($searchString) || mb_strlen($searchString) < $minCharacters) {
                return new JsonResponse([]);
            }

            /** @var AbstractBackend $backend */
            $backend = GeneralUtility::makeInstance(BackendUtility::getSearchBackend(), []);
            try {
                $maxItems = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('versatile_search', 'suggest.maxItems') ?? 10;
            } catch (ExtensionConfigurationPathDoesNotExistException $exception) {
                $maxItems = 10;
            }
            /** @var SiteLanguage $language */
            $language = $request->getAttribute('language');

            $suggestions = $backend->suggest($searchString, $maxItems, $language->getLanguageId());

            return new JsonResponse($suggestions);
        }

        return $handler->handle($request);
    }
}
