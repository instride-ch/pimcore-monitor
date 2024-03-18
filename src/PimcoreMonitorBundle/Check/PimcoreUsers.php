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
 * @license    https://github.com/instride-ch/pimcore-monitor/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\PimcoreMonitorBundle\Check;

use Carbon\Carbon;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Model\User;

class PimcoreUsers extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:users';

    public function __construct(protected bool $skip) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $users = [];
        $userListing = new User\Listing();

        foreach ($userListing->getUsers() as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $lastLogin = $user->getLastLogin();

            $users[] = [
                'name' => $user->getName(),
                'active' => $user->isActive(),
                'is_admin' => $user->isAdmin(),
                'last_login' => Carbon::createFromTimestampUTC($lastLogin)->toIso8601String(),
            ];
        }

        return new Success(\sprintf('There are %s Pimcore users in the system', \count($users)), $users);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Pimcore Users';
    }
}





