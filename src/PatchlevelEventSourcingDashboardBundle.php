<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle;

use Patchlevel\EventSourcingDashboardBundle\DependencyInjection\CustomMessageHeaderCompilerPass;
use Patchlevel\EventSourcingDashboardBundle\DependencyInjection\PatchlevelEventSourcingDashboardExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class PatchlevelEventSourcingDashboardBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CustomMessageHeaderCompilerPass());
    }

    public function getContainerExtension(): PatchlevelEventSourcingDashboardExtension
    {
        return new PatchlevelEventSourcingDashboardExtension();
    }
}
