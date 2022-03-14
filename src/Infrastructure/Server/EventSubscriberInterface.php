<?php

declare(strict_types=1);

namespace App\Infrastructure\Server;

interface EventSubscriberInterface
{
    public static function getSubscribedEvents();
}