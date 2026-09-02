<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle\Tests\Unit;

use Patchlevel\EventSourcing\Metadata\Message\MessageHeaderRegistry;
use Patchlevel\EventSourcingDashboardBundle\Controller\DashboardController;
use Patchlevel\EventSourcingDashboardBundle\Controller\DefaultController;
use Patchlevel\EventSourcingDashboardBundle\Controller\EventController;
use Patchlevel\EventSourcingDashboardBundle\Controller\InspectionController;
use Patchlevel\EventSourcingDashboardBundle\Controller\StoreController;
use Patchlevel\EventSourcingDashboardBundle\Controller\SubscriptionController;
use Patchlevel\EventSourcingDashboardBundle\DependencyInjection\PatchlevelEventSourcingDashboardExtension;
use Patchlevel\EventSourcingDashboardBundle\Message\Header\RequestIdHeader;
use Patchlevel\EventSourcingDashboardBundle\PatchlevelEventSourcingDashboardBundle;
use Patchlevel\EventSourcingBundle\DependencyInjection\PatchlevelEventSourcingExtension;
use Patchlevel\EventSourcingBundle\PatchlevelEventSourcingBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PatchlevelEventSourcingBundleTest extends TestCase
{
    public function testEmptyConfig(): void
    {
        $container = new ContainerBuilder();
        $bundle = new PatchlevelEventSourcingDashboardBundle();

        $bundle->build($container);

        $extension = new PatchlevelEventSourcingDashboardExtension();
        $extension->load([], $container);

        $container->compile();

        self::assertFalse($container->has(DashboardController::class));
        self::assertFalse($container->has(DefaultController::class));
        self::assertFalse($container->has(EventController::class));
        self::assertFalse($container->has(InspectionController::class));
        self::assertFalse($container->has(StoreController::class));
        self::assertFalse($container->has(SubscriptionController::class));
    }

    public function testEnabled(): void
    {
        $container = new ContainerBuilder();
        $this->compileContainer(
            $container,
            [
                'patchlevel_event_sourcing_dashboard' => [
                    'enabled' => true,
                ],
            ]
        );

        self::assertTrue($container->has(DashboardController::class));
        self::assertTrue($container->has(DefaultController::class));
        self::assertTrue($container->has(EventController::class));
        self::assertTrue($container->has(InspectionController::class));
        self::assertTrue($container->has(StoreController::class));
        self::assertTrue($container->has(SubscriptionController::class));

        /** @var MessageHeaderRegistry $messageHeaderRegistry */
        $messageHeaderRegistry = $container->get(MessageHeaderRegistry::class);
        self::assertTrue($messageHeaderRegistry->hasHeaderClass(RequestIdHeader::class));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function compileContainer(ContainerBuilder $container, array $config): void
    {
        $bundle = new PatchlevelEventSourcingBundle();
        $bundle->build($container);

        $bundle = new PatchlevelEventSourcingDashboardBundle();
        $bundle->build($container);

        $container->setParameter('kernel.project_dir', __DIR__);

        // services

        $extension = new PatchlevelEventSourcingExtension();
        $extension->load(
            [
                'patchlevel_event_sourcing' => [
                    'connection' => [
                        'url' => 'sqlite3:///:memory:',
                    ],
                ]
            ],
            $container
        );

        $extension = new PatchlevelEventSourcingDashboardExtension();
        $extension->load($config, $container);

        $compilerPassConfig = $container->getCompilerPassConfig();
        $compilerPassConfig->setRemovingPasses([]);
        $compilerPassConfig->addPass(new TestCaseAllPublicCompilerPass());

        $container->compile();
    }
}
