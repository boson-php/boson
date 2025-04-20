<?php

declare(strict_types=1);

namespace Boson\Event;

use Boson\Application;
use Boson\Dispatcher\Event;

/**
 * @template-extends Event<Application>
 */
abstract class ApplicationEvent extends Event
{
    public function __construct(Application $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
