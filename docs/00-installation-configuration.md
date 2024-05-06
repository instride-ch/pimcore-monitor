# Installation & Bundle Configuration

### Install with Composer

```
composer require instride/pimcore-monitor:^3.0
```

### Loading the Bundle

Register the bundle in your `config/bundles.php` file to enable it.

```php
<?php

return [
    // ...
    Instride\Bundle\PimcoreMonitorBundle\PimcoreMonitorBundle::class => ['all' => true],
];
```

### Report Configuration

This bundle allows you to send a health report to a custom REST endpoint.

```yaml
pimcore_monitor:
    report:

        # API key for health report endpoint.
        api_key: '<YOUR_RANDOM_BEARER_TOKEN>'

        # Default health report API endpoint to send data to.
        default_endpoint: 'https://health.example.com/report'

        # Environment description for the instance (e.g. 'prod', 'test', 'dev').
        instance_environment: 'prod'
```

> **Note:** The health report is triggered with the command `pimcore:monitor:health-report`.
> Learn more about the available commands [here](02-commands.md).
