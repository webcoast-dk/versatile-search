<?php

namespace WEBcoast\VersatileSearch\Result;

interface ResultEnricherInterface {
    public function enrich(array $rawResult, string $tableName, array $resultItem): array;
}
