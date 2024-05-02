<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface WriteServiceInterface
{
    public function write(array $resources, array $properties, bool $autoSize, array $metadata, array $style, array $security): Spreadsheet;
}
