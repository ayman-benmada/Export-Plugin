<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use Abenmada\ExportPlugin\Entity\ExportTypeInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final readonly class SaveService implements SaveServiceInterface
{
    public function __construct(private WriteServiceInterface $writeService, private string $projectDir)
    {
    }

    /**
     * @throws Exception
     */
    public function save(array $resources, array $properties, string $fileName, array $configuration): void
    {
        $path = $configuration['path'];
        $type = $configuration['type'];

        $autoSize = $configuration['auto_size'];
        $metadata = $configuration['metadata'] ?? [];
        $style = $configuration['style'] ?? [];
        $security = $configuration['security'] ?? [];

        $fileName .= '.' . $type;

        $spreadsheet = $this->writeService->write($resources, $properties, $autoSize, $metadata, $style, $security);

        $writer = match ($type) {
            ExportTypeInterface::XLS => new Xls($spreadsheet),
            ExportTypeInterface::CSV => new Csv($spreadsheet),
            ExportTypeInterface::HTML => new Html($spreadsheet),
            ExportTypeInterface::ODS => new Ods($spreadsheet),
            ExportTypeInterface::PDF => IOFactory::createWriter($spreadsheet, 'Mpdf'),
            default => new Xlsx($spreadsheet),
        };

        $writer->save($this->projectDir . '/' . $path . '/' . $fileName);
    }
}
