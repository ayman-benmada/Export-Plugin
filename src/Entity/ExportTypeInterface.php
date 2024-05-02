<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Entity;

interface ExportTypeInterface
{
    public const XLSX = 'xlsx';

    public const XLS = 'xls';

    public const CSV = 'csv';

    public const HTML = 'html';

    public const ODS = 'ods';

    public const PDF = 'pdf';
}
