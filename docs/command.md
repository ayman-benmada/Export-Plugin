Command Reference
=======================

The following command saves the exported data :

```bash
bin/console abenmada:export_plugin:save <alias>
```

- The **alias** argument is **required**, make sure you have defined the save configuration for this alias at
  **abenmada_export_plugin**.
- The command will be executed even if saving is **not enabled** in the configuration file for this alias.
