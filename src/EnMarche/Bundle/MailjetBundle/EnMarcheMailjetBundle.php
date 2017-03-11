<?php

namespace EnMarche\Bundle\MailjetBundle;

use EnMarche\Bundle\MailjetBundle\DependencyInjection\CompilerPass\RegisterClientsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnMarcheMailjetBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterClientsPass());
    }
}
