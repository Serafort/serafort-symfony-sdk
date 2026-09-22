<?php

declare(strict_types=1);

namespace Serafort\Symfony\Security;

use Serafort\Symfony\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class SerafortAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') &&
            str_starts_with((string) $request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = (string) $request->headers->get('Authorization');
        $token = trim(substr($authHeader, 7));

        if ($token === '') {
            throw new AuthenticationException('No Bearer token provided in Authorization header.');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function (string $userIdentifier) {
                try {
                    return $this->client->validateToken($userIdentifier);
                } catch (\Throwable $e) {
                    throw new AuthenticationException($e->getMessage(), 0, $e);
                }
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Allow request to continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Unauthorized',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
