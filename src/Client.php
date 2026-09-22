<?php

declare(strict_types=1);

namespace Serafort\Symfony;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client as HttpClient;
use RuntimeException;
use Serafort\Symfony\Security\SerafortUser;

class Client
{
    private HttpClient $http;
    /** @var array<string, Key>|null */
    private ?array $cachedKeys = null;

    public function __construct(
        public readonly string $endpoint,
        public readonly ?string $clientId = null,
        public readonly ?string $clientSecret = null,
        public readonly int $leeway = 60,
        ?HttpClient $httpClient = null
    ) {
        $this->http = $httpClient ?? new HttpClient([
            'base_uri' => rtrim($this->endpoint, '/') . '/',
            'timeout' => 10.0,
        ]);
        JWT::$leeway = $this->leeway;
    }

    public function validateToken(string $token): SerafortUser
    {
        $keys = $this->getJwksKeys();
        try {
            $decoded = (array) JWT::decode($token, $keys);
            return SerafortUser::fromJwtPayload($decoded);
        } catch (\Throwable $e) {
            throw new RuntimeException("Token validation failed: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @return array<string, Key>
     */
    public function getJwksKeys(): array
    {
        if ($this->cachedKeys !== null) {
            return $this->cachedKeys;
        }

        $response = $this->http->get('.well-known/jwks.json');
        $jwksData = json_decode((string) $response->getBody(), true);

        if (!is_array($jwksData) || !isset($jwksData['keys'])) {
            throw new RuntimeException('Invalid JWKS payload received from Serafort endpoint.');
        }

        $this->cachedKeys = JWK::parseKeySet($jwksData);
        return $this->cachedKeys;
    }
}
