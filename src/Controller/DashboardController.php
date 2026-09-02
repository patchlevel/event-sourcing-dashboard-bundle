<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle\Controller;

use Patchlevel\EventSourcing\Metadata\AggregateRoot\AggregateRootRegistry;
use Patchlevel\EventSourcing\Metadata\Event\EventRegistry;
use Patchlevel\EventSourcing\Store\Store;
use Patchlevel\EventSourcing\Subscription\Engine\SubscriptionEngine;
use Patchlevel\EventSourcing\Subscription\Status;
use Patchlevel\EventSourcing\Subscription\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

use function count;

final readonly class DashboardController
{
    private const LATEST_MESSAGE_LIMIT = 10;

    public function __construct(
        private Environment $twig,
        private Store $store,
        private AggregateRootRegistry $aggregateRootRegistry,
        private EventRegistry $eventRegistry,
        private SubscriptionEngine $engine,
    ) {
    }

    public function indexAction(): Response
    {
        $subscriptions = $this->engine->subscriptions();

        return new Response(
            $this->twig->render('@PatchlevelEventSourcingDashboard/dashboard/index.html.twig', [
                'messageCount' => $this->store->count(),
                'aggregateCount' => count($this->aggregateRootRegistry->aggregateNames()),
                'eventCount' => count($this->eventRegistry->eventNames()),
                'subscriptionCount' => count($subscriptions),
                'subscriptionStatuses' => $this->countByStatus($subscriptions),
                'messages' => $this->store->load(null, self::LATEST_MESSAGE_LIMIT, 0, true),
            ]),
        );
    }

    /**
     * @param list<Subscription> $subscriptions
     *
     * @return array<string, int>
     */
    private function countByStatus(array $subscriptions): array
    {
        $counts = [];

        foreach (Status::cases() as $status) {
            $counts[$status->value] = 0;
        }

        foreach ($subscriptions as $subscription) {
            $counts[$subscription->status()->value]++;
        }

        return $counts;
    }
}
