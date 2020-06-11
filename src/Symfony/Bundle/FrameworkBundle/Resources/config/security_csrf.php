<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bridge\Twig\Extension\CsrfRuntime;
use Symfony\Bridge\Twig\Extension\CsrfExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('security.csrf.token_generator', UriSafeTokenGenerator::class)

        ->alias(TokenGeneratorInterface::class, 'security.csrf.token_generator')

        ->set('security.csrf.token_storage', SessionTokenStorage::class)
            ->args([service('session')])

        ->alias(TokenStorageInterface::class, 'security.csrf.token_storage')

        ->set('security.csrf.token_manager', CsrfTokenManager::class)
            ->public()
            ->args([
                service('security.csrf.token_generator'),
                service('security.csrf.token_storage'),
                service('request_stack')->ignoreOnInvalid()
            ])

        ->alias(CsrfTokenManagerInterface::class, 'security.csrf.token_manager')

        ->set('twig.runtime.security_csrf', CsrfRuntime::class)
            ->args([service('security.csrf.token_manager')])
            ->tag('twig.runtime')

        ->set('twig.extension.security_csrf', CsrfExtension::class)
            ->tag('twig.extension')
    ;
};