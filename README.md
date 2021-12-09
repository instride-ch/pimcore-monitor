![Pimcore Monitor Bundle](docs/images/github_banner.png "Pimcore Monitor Bundle")

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/w-vision/pimcore-monitor-bundle.svg?style=flat-square)](https://packagist.org/packages/w-vision/pimcore-monitor-bundle)

This bundle provides a way to run a series of Pimcore and application related health checks. Each health check should
implement some application logic that you want to make sure always works. Another usage can be testing for specific
requirements, like availability of PHP extensions.

## Available Checks
- **AppEnvironment:** Checks whether the application is running in production mode.
- **DiskUsage:** Checks how much space is being allocated on the disk.
- **DoctrineMigrations:** Checks whether all Doctrine Migrations have been migrated.
- **HostingSize:** Checks how much Disk space is used by this hosting.
- **HttpsConnection:** Checks whether the HTTPS encryption is enabled.
- **MySqlVersion:** Checks what MySQL version is configured.
- **PhpVersion:** Checks what PHP version is configured.
- **PimcoreAreabricks:** Checks which Areabricks are installed within Pimcore.
- **PimcoreBundles:** Checks which Bundles are installed within Pimcore.
- **PimcoreElementCount:** Checks whether the count of Pimcore Elements exceeds a certain threshold.
- **PimcoreMaintenance:** Checks whether Pimcore maintenance is enabled.
- **PimcoreUsers:** Checks what Pimcore Users are configured.
- **PimcoreVersion:** Checks what Pimcore Version is installed.

## Further Information
* [Installation & Bundle Configuration](docs/00-installation-configuration.md)
* [Adding and Running Checks](docs/01-adding-and-running-checks.md)
* [Adding additional Reporters](docs/02-adding-additional-reporters.md)
* [Grouping Checks](docs/03-grouping-checks.md)
* [Commands](docs/04-commands.md)

## License
**w-vision AG**, Sandgruebestrasse 4, 6210 Sursee, Switzerland  
support@w-vision.ch, [w-vision.ch](https://www.w-vision.ch)  
Copyright © 2022 w-vision AG. All rights reserved.

For licensing details please visit [LICENSE.md](LICENSE.md) 
