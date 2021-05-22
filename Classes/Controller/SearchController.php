<?php

namespace WEBcoast\VersatileSearch\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WEBcoast\VersatileSearch\Backend\AbstractBackend;
use WEBcoast\VersatileSearch\Utility\BackendUtility;

class SearchController extends ActionController
{
    public function searchAction()
    {
        $searchString = GeneralUtility::_GP($this->settings['parameters']['search'] ?? 'q');

        if (!empty(trim($searchString))) {
            /** @var AbstractBackend $backend */
            $backend = GeneralUtility::makeInstance(BackendUtility::getSearchBackend(), $this->settings);
            $searchResults = $backend->search($searchString);
        } else {
            $searchResults = [];
        }

        $this->view->assign('searchString', $searchString);
        $this->view->assignMultiple($searchResults);
    }

    public function formAction()
    {
        // This action has no logic. It just displays the static search form and is cached.
    }
}
