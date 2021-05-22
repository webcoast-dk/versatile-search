<?php

namespace WEBcoast\VersatileSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use WEBcoast\VersatileSearch\Utility\BackendUtility;

class ConvertRawResultViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('raw', 'array', 'The raw search result item', true);
        $this->registerArgument('as', 'string', 'The variable name to store the converted search result in', false, 'resultItem');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $backend = BackendUtility::getSearchBackend();
        $renderingContext->getVariableProvider()->add($arguments['as'], $backend::fetchResult($arguments['raw']));

        $output = $renderChildrenClosure();

        $renderingContext->getVariableProvider()->remove($arguments['as']);

        return $output;
    }
}
