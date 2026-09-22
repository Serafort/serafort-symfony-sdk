<?php

declare(strict_types=1);

namespace Serafort\Symfony\DependencyInjection;

use Serafort\Symfony\Client;
use Serafort\Symfony\Security\SerafortAuthenticator;
use Serafort\Symfony\Security\SerafortVoter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SerafortExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $clientDef = new Definition(Client::class, [
            $config['endpoint'],
            $config['client_id'] ?? null,
            $config['client_secret'] ?? null,
            $config['leeway'] ?? 60,
        ]);
        $clientDef->setPublic(true);
        $container->setDefinition(Client::class, $clientDef);
        $container->setAlias('serafort.client', Client::class);

        $authenticatorDef = new Definition(SerafortAuthenticator::class, [
            $clientDef,
        ]);
        $container->setDefinition(SerafortAuthenticator::class, $authenticatorDef);

        $voterDef = new Definition(SerafortVoter::class);
        $voterDef->addTag('security.voter');
        $container->setDefinition(SerafortVoter::class, $voterDef);
    }
}
