# Serafort Bundle for Symfony

Enterprise IAM, Security Authenticator, Security Voter, and Wildcard RBAC for Symfony 6.4 and 7+.

## Features

- 🛡️ **Custom Authenticator**: `SerafortAuthenticator` parses Bearer tokens and validates against local JWKS.
- ⚡ **Security Voter**: `SerafortVoter` for fine-grained wildcard permission checks (`isGranted('SERAFORT_PERMISSION', 'org:*')`).
- 🏢 **Multi-Tenant Isolation**: Zero-leakage multi-tenant user context (`SerafortUser`).
- 🔌 **Dependency Injection**: Auto-configures `Client`, `SerafortAuthenticator`, and `SerafortVoter`.

## Installation

```bash
composer require serafort/symfony-bundle
```

Register bundle in `config/bundles.php`:

```php
return [
    // ...
    Serafort\Symfony\SerafortBundle::class => ['all' => true],
];
```

## Quick Start

### 1. Configure `config/packages/security.yaml`

```yaml
security:
    providers:
        serafort_user_provider:
            id: Serafort\Symfony\Security\SerafortAuthenticator

    firewalls:
        api:
            pattern: ^/api
            stateless: true
            custom_authenticators:
                - Serafort\Symfony\Security\SerafortAuthenticator
```

### 2. Protect Controllers

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/api/dashboard', methods: ['GET'])]
    #[IsGranted('SERAFORT_PERMISSION', subject: 'org:*')]
    public function index(): JsonResponse
    {
        /** @var \Serafort\Symfony\Security\SerafortUser $user */
        $user = $this->getUser();

        return $this->json([
            'userId' => $user->userId,
            'tenantId' => $user->tenantId,
        ]);
    }
}
```
