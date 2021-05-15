<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

/**
 * Class VersionProcessor.
 */
class VersionProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    private $version;

    /**
     * VersionProcessor constructor.
     */
    public function __construct(string $appVersion)
    {
        $this->version = $appVersion;
    }

    /**
     * @return array
     */
    public function __invoke(array $records)
    {
        list($major, $minor, $patch) = explode('.', $this->version);

        $records['extra']['version'] = ['major' => $major, 'minor' => $minor, 'patch' => $patch];

        return $records;
    }
}
