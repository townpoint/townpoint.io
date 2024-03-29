<?php

declare(strict_types = 1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'http_client' => [
            'default_options' => [
                'verify_peer' => false,
            ],
            'scoped_clients' => [
                'wikipedia.api.client' => [
                    'base_uri' => 'https://en.wikipedia.org/w/api.php',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ],
            ],
        ],
        'secret' => '%env(APP_SECRET)%',
        'http_method_override' => false,
        'session' => [
            'handler_id' => null,
            'cookie_secure' => 'auto',
            'cookie_samesite' => 'lax',
            'storage_factory_id' => 'session.storage.factory.native',
        ],
        'php_errors' => [
            'log' => true,
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('framework', [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);
    }
};
