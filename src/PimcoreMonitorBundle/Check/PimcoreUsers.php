<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Carbon\Carbon;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Model\User;

class PimcoreUsers extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:users';

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        $users = [];
        $userListing = new User\Listing();

        foreach ($userListing->getUsers() as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $lastLoginTs = $user->getLastLogin();
            $lastLogin = Carbon::createFromTimestampUTC($lastLoginTs);

            $users[] = [
                'name' => $user->getName(),
                'active' => $user->isActive(),
                'is_admin' => $user->isAdmin(),
                'last_login' => $lastLogin instanceof Carbon ? $lastLogin->toIso8601String() : $lastLoginTs,
            ];
        }

        return new Success(sprintf('There are %s Pimcore users in the system', \count($users)), $users);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Pimcore Users';
    }
}





