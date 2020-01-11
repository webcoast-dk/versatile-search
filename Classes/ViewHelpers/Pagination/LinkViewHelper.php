<?php

namespace WEBcoast\VersatileSearch\ViewHelpers\Pagination;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\PageViewHelper;

class LinkViewHelper extends PageViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('page', 'int', 'The page number in the pagination');
        $this->registerArgument('settings', 'array', 'The plugin settings', true);
    }

    public function render()
    {
        // Make sure, we always link to the current page
        $this->arguments['pageUid'] = null;
        $additionalParameters = $this->arguments['additionalParams'] ?? [];
        $additionalParameters[$this->arguments['settings']['parameters']['search']] = GeneralUtility::_GP($this->arguments['settings']['parameters']['search']);
        if ($this->arguments['page'] > 0) {
            $additionalParameters[$this->arguments['settings']['parameters']['page']] = $this->arguments['page'];
        }
        $this->arguments['additionalParams'] = $additionalParameters;
        // Disable cache hash
        $this->arguments['noCacheHash'] = true;

        return parent::render();
    }
}
