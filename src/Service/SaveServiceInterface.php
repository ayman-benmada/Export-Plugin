<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

interface SaveServiceInterface
{
    public function save(array $resources, array $properties, string $fileName, array $configuration): void;
}
