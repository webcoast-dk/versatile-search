<?php

declare(strict_types=1);

namespace WEBcoast\VersatileSearch\Pagination;

use TYPO3\CMS\Core\Pagination\AbstractPaginator;

class IndexedSearchPaginator extends AbstractPaginator
{
    protected $resultRows = [];

    protected $resultCount = 0;

    public function __construct(array $resultRows, int $resultCount, int $itemsPerPage, int $currentPage)
    {
        $this->resultRows = $resultRows;
        $this->resultCount = $resultCount;
        $this->setItemsPerPage($itemsPerPage);
        $this->setCurrentPageNumber($currentPage);

        $this->updateInternalState();
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        // Does nothing, as we there aren't any items to paginate
    }

    protected function getTotalAmountOfItems(): int
    {
        return $this->resultCount;
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return count($this->resultRows);
    }

    public function getPaginatedItems(): iterable
    {
        return $this->resultRows;
    }
}
