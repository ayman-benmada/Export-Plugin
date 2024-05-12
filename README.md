<h1>Sylius Export Plugin</h1>

<p>
    The Export plugin allows you to export all data of any entity directly from the configuration file.
</p>

![presentation photo](https://github.com/ayman-benmada/Sylius-Export-Plugin/blob/main/src/Resources/public/presentation.png?raw=true)

## Documentation

- <strong><a href="https://github.com/ayman-benmada/Sylius-Export-Plugin/blob/main/docs/configuration.md">Configuration Reference</a></strong>
- <strong><a href="https://github.com/ayman-benmada/Sylius-Export-Plugin/blob/main/docs/command.md">Command Reference</a></strong>

## Installation

Require plugin with composer :

```bash
composer require abenmada/sylius-export-plugin
```

Change your `config/bundles.php` file to add the line for the plugin :

```php
<?php

return [
    //..
    Abenmada\ExportPlugin\ExportPlugin::class => ['all' => true],
]
```

Then create the config file in `config/packages/abenmada_export_plugin.yaml` :

```yaml
imports:
    - { resource: "@ExportPlugin/Resources/config/services.yaml" }
```

Then import the routes in `config/routes/abenmada_export_plugin.yaml` :

```yaml
abenmada_export_plugin_routing:
    resource: "@ExportPlugin/Resources/config/routes.yaml"
    prefix: /%sylius_admin.path_name%
```
