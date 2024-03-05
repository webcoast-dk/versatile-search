<?php

namespace WEBcoast\VersatileSearch\Controller;

use GeorgRinger\NumberedPagination\NumberedPagination;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WEBcoast\VersatileSearch\Backend\AbstractBackend;
use WEBcoast\VersatileSearch\Utility\BackendUtility;

class SearchController extends ActionController
{
    public function searchAction()
    {
        $searchString = $this->request->getQueryParams()[($this->settings['parameters']['search'] ?? 'q')];
        $currentPage = $this->request->getQueryParams()[($this->settings['parameters']['page'] ?? 'p')];
        $currentPage = max(1, $currentPage ? (int)$currentPage : 1);
        $category = $this->request->getQueryParams()[($this->settings['parameters']['category'] ?? 'c')];
        $maximumNumberOfLinks = (int)($this->settings['pagination']['maximumNumberOfLinks'] ?? 7);

        if (!empty(trim($searchString))) {
            /** @var AbstractBackend $backend */
            $backend = GeneralUtility::makeInstance(BackendUtility::getSearchBackend(), $this->settings);
            $searchResults = $backend->search(mb_strtolower($searchString), $currentPage, $category);
        } else {
            $searchResults = [];
        }

        $paginationClass = $this->settings['pagination']['class'] ?? SimplePagination::class;
        if (count($searchResults['categories'] ?? []) > 0) {
            foreach($searchResults['categories'] as &$categoryResult) {
                if (array_key_exists('paginator', $categoryResult) && $categoryResult['paginator'] instanceof PaginatorInterface) {
                    $categoryResult['pagination'] = $this->getPagination($paginationClass, $maximumNumberOfLinks, $categoryResult['paginator']);
                }
            }
        } elseif (array_key_exists('paginator', $searchResults) && $searchResults['paginator'] instanceof PaginatorInterface) {
            $searchResults['pagination'] = $this->getPagination($paginationClass, $maximumNumberOfLinks, $searchResults['paginator']);
        }

        $this->view->assign('searchString', $searchString);
        $this->view->assignMultiple($searchResults);

        return $this->htmlResponse();
    }

    public function formAction()
    {
        // This action has no logic. It just displays the static search form and is cached.

        return $this->htmlResponse();
    }

    protected function getPagination($paginationClass, $maximumNumberOfLinks, $paginator)
    {
        if (class_exists(NumberedPagination::class) && $paginationClass === NumberedPagination::class && $maximumNumberOfLinks) {
            $pagination = GeneralUtility::makeInstance(NumberedPagination::class, $paginator, $maximumNumberOfLinks);
        } elseif (class_exists($paginationClass)) {
            $pagination = GeneralUtility::makeInstance($paginationClass, $paginator);
        } else {
            $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);
        }
        return $pagination;
    }
}
