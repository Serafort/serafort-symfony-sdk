<?php

declare(strict_types=1);

namespace Serafort\Symfony\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class SerafortUser implements UserInterface
{
    /**
     * @param string $userId
     * @param string $tenantId
     * @param array<string> $roles
     * @param array<string> $permissions
     * @param array<string, mixed> $claims
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $tenantId,
        public readonly array $roles = [],
        public readonly array $permissions = [],
        public readonly array $claims = []
    ) {}

    public static function fromJwtPayload(array $payload): self
    {
        $rawRoles = $payload['roles'] ?? [];
        $roles = array_map(function ($r) {
            return str_starts_with($r, 'ROLE_') ? $r : 'ROLE_' . strtoupper($r);
        }, $rawRoles);

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return new self(
            userId: $payload['sub'] ?? $payload['user_id'] ?? '',
            tenantId: $payload['tenant_id'] ?? '',
            roles: $roles,
            permissions: $payload['permissions'] ?? [],
            claims: $payload
        );
    }

    public function getUserIdentifier(): string
    {
        return $this->userId;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void {}

    /**
     * Wildcard permission evaluator (e.g. 'org:*' matches 'org:users:create').
     */
    public function hasPermission(string $required): bool
    {
        foreach ($this->permissions as $perm) {
            if ($perm === '*' || $perm === $required) {
                return true;
            }
            if (str_ends_with($perm, ':*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($required, $prefix . ':') || $required === $prefix) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hasTenant(string $tenantId): bool
    {
        return $this->tenantId === $tenantId;
    }
}
