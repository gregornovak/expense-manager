<?php

declare(strict_types=1);

namespace App\Utils;

class PaginatorChecker
{
    private $minPage = 1;
    private $maxPage = 1000;
    private $minLimit = 5;
    private $maxLimit = 20;

    public function isWithinRange(int $page, int $limit): bool
    {
        if ($page >= $this->minPage && $page <= $this->maxPage && $limit >= $this->minLimit && $limit <= $this->maxLimit) {
            return true;
        }

        return false;
    }
}
