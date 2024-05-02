<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Controller;

use Abenmada\ExportPlugin\Service\ProcessServiceInterface;
use function is_string;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ExportController
{
    public function __construct(private ProcessServiceInterface $processService)
    {
    }

    public function __invoke(Request $request): RedirectResponse|StreamedResponse
    {
        $alias = $request->attributes->get('alias');
        $redirectUrl = $request->headers->get('referer');

        if (!is_string($alias)) {
            throw new UnexpectedTypeException($alias, 'string');
        }

        return $this->processService->process($alias, $redirectUrl);
    }
}
