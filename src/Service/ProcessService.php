<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Service;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function explode;
use function str_replace;
use function time;
use function ucfirst;
use function usort;

final class ProcessService implements ProcessServiceInterface
{
    private const DEFAULT_METHOD = 'findAll';

    public function __construct(
        private DownloadServiceInterface $downloadService,
        private SaveServiceInterface $saveService,
        private FlashBagInterface $flashBag,
        private TranslatorInterface $translator,
        private RouterInterface $router,
        private ContainerInterface $container,
        private array $aliasConfigurations
    ) {
    }

    public function process(string $alias, ?string $redirectUrl = null, bool $callFromSaveCommand = false): RedirectResponse|StreamedResponse
    {
        if ($redirectUrl === null) {
            $redirectUrl = $this->router->generate('sylius_admin_dashboard');
        }

        $configuration = $this->aliasConfigurations[$alias] ?? [];

        // Properties
        $properties = [];
        foreach ($configuration['properties'] as $name => $definition) {
            if (! $definition['enabled']) {
                continue;
            }

            $properties[] = [
                'name' => $name,
                'getter' => $this->getter($name, $definition),
                'label' => $definition['label'],
                'position' => $definition['position'],
                'options' => $definition['options'] ?? [],
            ];
        }

        // Sorting by position
        usort($properties, static fn ($a, $b) => $a['position'] > $b['position']); // @phpstan-ignore-line

        $resources = $this->getResources($configuration, $configuration['model']);

        // Save
        if (isset($configuration['save']) && $configuration['save']['enabled']) {
            $fileName = $this->getFileName($configuration['save']);
            $this->saveService->save($resources, $properties, $fileName, $configuration['save']);

            if (! $callFromSaveCommand) {
                $this->flashBag->add('success', $this->translator->trans('abenmada_export_plugin.' . $alias . '.export', [], 'flashes'));
            }
        }

        // Download
        if (! $callFromSaveCommand && isset($configuration['download']) && $configuration['download']['enabled']) {
            $fileName = $this->getFileName($configuration['download']);

            return $this->downloadService->download($resources, $properties, $fileName, $configuration['download']);
        }

        return new RedirectResponse($redirectUrl, 302);
    }

    private function getFileName(array $configuration): string
    {
        $timeStamp = time();
        $fileName = $configuration['file_name'];

        if ($configuration['prefix_timestamp']) {
            $fileName = $timeStamp . '_' . $fileName;
        }

        if ($configuration['suffix_timestamp']) {
            $fileName .= '_' . $timeStamp;
        }

        return $fileName;
    }

    private function getter(string $name, array $definition): string
    {
        $getter = array_key_exists('path', $definition) ? $definition['path'] : 'get' . ucfirst($name);

        return str_replace('()', '', $getter);
    }

    private function getResources(array $configuration, string $model): array
    {
        $model = explode('.', $model);

        $repositoryAlias = $model[0] . '.repository.' . $model[1];

        /** @var RepositoryInterface $repository */
        $repository = $this->container->get($repositoryAlias);

        $method = self::DEFAULT_METHOD;
        $arguments = [];

        if (array_key_exists('repository', $configuration)) {
            $method = $configuration['repository']['method'];
            $arguments = $configuration['repository']['arguments'];
        }

        $result = $repository->$method(...$arguments); // @phpstan-ignore-line

        if ($result instanceof QueryBuilder) {
            return $result->getQuery()->getResult();
        }

        return $result;
    }
}
