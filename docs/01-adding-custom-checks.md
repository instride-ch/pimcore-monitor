# Adding Custom Checks

A Check class MUST extend the [`AbstractCheck`](../src/PimcoreMonitorBundle/Check/AbstractCheck.php)
and provide the following methods in addition to a unique identifier.

```php
<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\ResultInterface;

class MyCustomCheck extends AbstractCheck
{
    protected const IDENTIFIER = 'some_category:my_custom_check';

    /**
     * Do your check logic here and return a result.
     */
    public function check(): ResultInterface
    {
        // Your code ...
    }

    /**
     * Return a nice label describing this health check.
     */
    public function getLabel(): string
    {
        return 'My Custom Check';
    }
}
```

The main `check()` method is responsible for performing the actual check, and is expected to return
a `ResultInterface` instance. It is recommended to use the built-in result classes for compatibility
with the diagnostics Runner and other checks.

> **Note:** This bundle ships with many [checks](../src/PimcoreMonitorBundle/Check) that can serve
> as an example of how to write your own checks.