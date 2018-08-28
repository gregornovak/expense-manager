<?php

declare (strict_types = 1);

namespace App\Utils;

class ExpenseAmountAggregator
{
    public function aggregate(array $data): array
    {
        $aggregated = [];

        foreach ($data as $value) {
            $day = $value['added']->format('j');
            if (isset($aggregated[$day])) {
                $aggregated[$day]['day'] = $day;
                $aggregated[$day]['amount'] += $value['amount'];
                $aggregated[$day]['added'] = $value['added'];
            } else {
                $aggregated[$day]['day'] = $day;
                $aggregated[$day]['amount'] = $value['amount'];
                $aggregated[$day]['added'] = $value['added'];
            }
        }

        return $aggregated;
    }
}
