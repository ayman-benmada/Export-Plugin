<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface DownloadServiceInterface
{
    public function download(array $resources, array $properties, string $fileName, array $configuration): StreamedResponse;
}
