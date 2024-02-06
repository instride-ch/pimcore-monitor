<?php

declare(strict_types=1);

/**
 * Pimcore Monitor
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  2024 instride AG (https://instride.ch)
 * @license    https://github.com/instride-ch/PimcoreMonitorBundle/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Deployer;

desc('Runs the health report, if not disabled');
task('pimcore:monitor:health-report', static function () {
    run('cd {{release_path}} && {{bin/console}} pimcore:monitor:health-report');
})->select('disable!=monitor_health_report');

task('pimcore:monitor', [
    'pimcore:monitor:health-report',
]);

after('deploy:success', 'pimcore:monitor');
