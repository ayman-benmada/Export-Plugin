<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Abenmada\ExportPlugin\Entity\ExportTypeInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadService implements DownloadServiceInterface
{
    public function __construct(private WriteServiceInterface $writeService)
    {
    }

    /**
     * @throws Exception
     */
    public function download(array $resources, array $properties, string $fileName, array $configuration): StreamedResponse
    {
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

        $response = new StreamedResponse(static fn () => $writer->save('php://output'));

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
