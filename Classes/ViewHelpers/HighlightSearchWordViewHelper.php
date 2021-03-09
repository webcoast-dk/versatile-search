<?php

namespace WEBcoast\VersatileSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class HighlightSearchWordViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    protected $escapeChildren = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('searchWords', 'array', 'The search word to highlight', true);
        $this->registerArgument('wrap', 'string', 'The wrap around the search word', false, '<span class="result__highlight">|</span>');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        // Get the content, either from the argument or rendered children
        $content = $renderChildrenClosure();
        // Split the wrap value into before and after
        [$before, $after] = explode('|', $arguments['wrap']);
        // Replace all occurrences of the search words with the search word wrapped in `wrap`
        foreach ($arguments['searchWords'] as $searchWord) {
            $content = preg_replace('/(' . $searchWord . ')/i', $before . '\1' . $after, $content);
        }

        return $content;
    }
}
