<?php

declare(strict_types=1);

namespace Serafort\Symfony\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SerafortVoter extends Voter
{
    public const PERMISSION = 'SERAFORT_PERMISSION';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::PERMISSION && is_string($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SerafortUser) {
            return false;
        }

        /** @var string $permission */
        $permission = $subject;

        return $user->hasPermission($permission);
    }
}
