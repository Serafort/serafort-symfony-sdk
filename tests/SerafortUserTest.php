<?php

declare(strict_types=1);

namespace Serafort\Symfony\Tests;

use PHPUnit\Framework\TestCase;
use Serafort\Symfony\Security\SerafortUser;

class SerafortUserTest extends TestCase
{
    public function testWildcardPermissions(): void
    {
        $user = new SerafortUser(
            userId: 'usr_sym_456',
            tenantId: 'tenant_symfony',
            roles: ['ROLE_ADMIN'],
            permissions: ['org:*', 'reports:export']
        );

        // Wildcard
        $this->assertTrue($user->hasPermission('org:users:create'));
        $this->assertTrue($user->hasPermission('org:settings:read'));

        // Exact
        $this->assertTrue($user->hasPermission('reports:export'));

        // Non-match
        $this->assertFalse($user->hasPermission('reports:delete'));
        $this->assertFalse($user->hasPermission('system:root'));
    }

    public function testRolesNormalization(): void
    {
        $payload = [
            'sub' => 'usr_sym_789',
            'tenant_id' => 'tenant_acme',
            'roles' => ['admin', 'manager'],
            'permissions' => ['org:*'],
        ];

        $user = SerafortUser::fromJwtPayload($payload);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertSame('usr_sym_789', $user->getUserIdentifier());
    }
}
