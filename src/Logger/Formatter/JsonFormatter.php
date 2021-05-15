<?php

declare(strict_types=1);

namespace App\Logger\Formatter;

use Monolog\Formatter\JsonFormatter as BaseFormatter;

/**
 * Class JsonFormatter.
 */
class JsonFormatter extends BaseFormatter
{
    public function format(array $record): string
    {
        $record = array_merge($record, $record['extra'] ?? []);
        $record['level'] = strtolower($record['level_name']);
        $record['datetime'] = $record['datetime']->format('c');
        unset($record['extra'], $record['level_name']);

        return parent::format($record);
    }
}
