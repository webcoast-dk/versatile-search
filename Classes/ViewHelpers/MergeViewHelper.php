<?php

declare(strict_types=1);


namespace WEBcoast\VersatileSearch\ViewHelpers;


use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MergeViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('base', 'array', 'The base array');
        $this->registerArgument('merge', 'array', 'The array to be merged into base');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return array_replace_recursive($arguments['base'] ?? [], $arguments['merge'] ?? []);
    }
}
