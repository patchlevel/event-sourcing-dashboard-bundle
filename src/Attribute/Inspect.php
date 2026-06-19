<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle\Attribute;

use Attribute;
use Patchlevel\EventSourcingDashboardBundle\Color;

#[Attribute(Attribute::TARGET_CLASS)]
final class Inspect
{
    public function __construct(
        public readonly string|null $description = null,
        public readonly string|null $icon = null,
        public readonly string|Color|null $color = null,
        public readonly string|null $size = null,
    ) {
    }
}
