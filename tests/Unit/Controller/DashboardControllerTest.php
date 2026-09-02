<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle\Tests\Unit\Controller;

use Patchlevel\EventSourcing\Metadata\AggregateRoot\AggregateRootRegistry;
use Patchlevel\EventSourcing\Metadata\Event\EventRegistry;
use Patchlevel\EventSourcing\Store\ArrayStream;
use Patchlevel\EventSourcing\Store\Store;
use Patchlevel\EventSourcing\Subscription\Engine\SubscriptionEngine;
use Patchlevel\EventSourcing\Subscription\Status;
use Patchlevel\EventSourcing\Subscription\Subscription;
use Patchlevel\EventSourcingDashboardBundle\Controller\DashboardController;
use Patchlevel\EventSourcingDashboardBundle\Tests\Fixture\Order;
use Patchlevel\EventSourcingDashboardBundle\Tests\Fixture\OrderPaid;
use Patchlevel\EventSourcingDashboardBundle\Tests\Fixture\OrderPlaced;
use Patchlevel\EventSourcingDashboardBundle\Tests\Fixture\Profile;
use Patchlevel\EventSourcingDashboardBundle\Tests\Fixture\ProfileCreated;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class DashboardControllerTest extends TestCase
{
    public function testRendersCountsForAllStatuses(): void
    {
        $subscriptions = [
            new Subscription('a', status: Status::Active),
            new Subscription('b', status: Status::Active),
            new Subscription('c', status: Status::Paused),
            new Subscription('d', status: Status::Error),
        ];

        $stream = new ArrayStream([]);

        $store = $this->createMock(Store::class);
        $store->method('count')->willReturn(42);
        $store->expects($this->once())
            ->method('load')
            ->with(null, 10, 0, true)
            ->willReturn($stream);

        $engine = $this->createMock(SubscriptionEngine::class);
        $engine->method('subscriptions')->willReturn($subscriptions);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with(
                '@PatchlevelEventSourcingDashboard/dashboard/index.html.twig',
                [
                    'messageCount' => 42,
                    'aggregateCount' => 2,
                    'eventCount' => 3,
                    'subscriptionCount' => 4,
                    'subscriptionStatuses' => [
                        'new' => 0,
                        'booting' => 0,
                        'active' => 2,
                        'paused' => 1,
                        'finished' => 0,
                        'detached' => 0,
                        'error' => 1,
                        'failed' => 0,
                    ],
                    'messages' => $stream,
                ],
            )
            ->willReturn('<html/>');

        $controller = new DashboardController(
            $twig,
            $store,
            new AggregateRootRegistry(['profile' => Profile::class, 'order' => Order::class]),
            new EventRegistry([
                'profile.created' => ProfileCreated::class,
                'order.placed' => OrderPlaced::class,
                'order.paid' => OrderPaid::class,
            ]),
            $engine,
        );

        $response = $controller->indexAction();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<html/>', $response->getContent());
    }

    public function testEveryStatusIsReportedWhenNothingIsRegistered(): void
    {
        $store = $this->createMock(Store::class);
        $store->method('count')->willReturn(0);
        $store->method('load')->willReturn(new ArrayStream([]));

        $engine = $this->createMock(SubscriptionEngine::class);
        $engine->method('subscriptions')->willReturn([]);

        $expectedStatuses = [];
        foreach (Status::cases() as $status) {
            $expectedStatuses[$status->value] = 0;
        }

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with(
                self::anything(),
                self::callback(static function (array $context) use ($expectedStatuses): bool {
                    self::assertSame(0, $context['messageCount']);
                    self::assertSame(0, $context['aggregateCount']);
                    self::assertSame(0, $context['eventCount']);
                    self::assertSame(0, $context['subscriptionCount']);
                    self::assertSame($expectedStatuses, $context['subscriptionStatuses']);

                    return true;
                }),
            )
            ->willReturn('');

        $controller = new DashboardController(
            $twig,
            $store,
            new AggregateRootRegistry([]),
            new EventRegistry([]),
            $engine,
        );

        $controller->indexAction();
    }
}
