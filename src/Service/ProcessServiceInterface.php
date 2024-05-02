<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ProcessServiceInterface
{
    public function process(string $alias, ?string $redirectUrl = null, bool $callFromSaveCommand = false): RedirectResponse|StreamedResponse;
}
