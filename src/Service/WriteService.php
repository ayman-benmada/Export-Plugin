<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use DateTime;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function explode;
use function range;

final class WriteService implements WriteServiceInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @throws Exception
     */
    public function write(array $resources, array $properties, bool $autoSize, array $metadata, array $style, array $security): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $this->setSpreadsheetMetadata($spreadsheet, $metadata);
        $this->setDefaultStyle($spreadsheet, $style);
        $this->setSecurity($spreadsheet, $security);

        $sheet = $spreadsheet->getActiveSheet();

        $col = 'A';

        foreach ($properties as $property) {
            $coordinate = $col++ . '1'; // @phpstan-ignore-line

            $sheet->setCellValue($coordinate, $this->translator->trans($property['label']));
            $sheet->getStyle($coordinate)->getAlignment()->setWrapText(true);
        }

        foreach ($resources as $key => $resource) {
            $col = 'A';

            foreach ($properties as $property) {
                $coordinate = $col++ . ($key + 2); // @phpstan-ignore-line

                $sheet->setCellValue($coordinate, $this->getCellValue($resource, $property['getter'], $property['options']));
                $sheet->getStyle($coordinate)->getAlignment()->setWrapText(true);
            }
        }

        if ($autoSize) {
            foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        $defaultSize = $spreadsheet->getDefaultStyle()->getFont()->getSize() ?? 11.0;
        foreach (range(1, $sheet->getHighestDataRow()) as $row) {
            $sheet->getRowDimension($row)->setRowHeight($defaultSize + 2);
        }

        return $spreadsheet;
    }

    private function getCellValue(ResourceInterface $resource, string $propertyGetter, array $options): mixed
    {
        $getters = explode('.', $propertyGetter);
        $value = $resource;

        foreach ($getters as $getter) {
            if ($value === null) {
                return null;
            }

            $value = $value->$getter(); // @phpstan-ignore-line
        }

        if ($value instanceof DateTime && isset($options['format'])) {
            $value = $value->format($options['format']);
        }

        return $value;
    }

    private function setSpreadsheetMetadata(Spreadsheet $spreadsheet, array $metadata): void
    {
        $properties = $spreadsheet->getProperties();

        if (array_key_exists('creator', $metadata)) {
            $properties->setCreator($metadata['creator']);
        }

        if (array_key_exists('lastModifiedBy', $metadata)) {
            $properties->setLastModifiedBy($metadata['lastModifiedBy']);
        }

        if (array_key_exists('title', $metadata)) {
            $properties->setTitle($metadata['title']);
        }

        if (array_key_exists('subject', $metadata)) {
            $properties->setSubject($metadata['subject']);
        }

        if (array_key_exists('description', $metadata)) {
            $properties->setDescription($metadata['description']);
        }

        if (array_key_exists('keywords', $metadata)) {
            $properties->setKeywords($metadata['keywords']);
        }

        if (array_key_exists('category', $metadata)) {
            $properties->setCategory($metadata['category']);
        }

        if (array_key_exists('manager', $metadata)) {
            $properties->setManager($metadata['manager']);
        }

        if (array_key_exists('company', $metadata)) {
            $properties->setCompany($metadata['company']);
        }

        $spreadsheet->setProperties($properties);
    }

    /**
     * @throws Exception
     */
    private function setDefaultStyle(Spreadsheet $spreadsheet, array $style): void
    {
        if (! array_key_exists('size', $style)) {
            return;
        }

        $spreadsheet->getDefaultStyle()->getFont()->setSize($style['size']);
    }

    private function setSecurity(Spreadsheet $spreadsheet, array $security): void
    {
        if (! array_key_exists('enabled', $security)) {
            return;
        }

        if (! $security['enabled']) {
            return;
        }

        if (! array_key_exists('password', $security)) {
            return;
        }

        $protection = $spreadsheet->getActiveSheet()->getProtection();
        $protection->setPassword($security['password']);
        $protection->setSheet(true);
        $protection->setSort(true);
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
    }
}
