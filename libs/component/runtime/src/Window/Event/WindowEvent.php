<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Dispatcher\Event;
use Boson\Window\Window;

/**
 * @template-extends Event<Window>
 */
abstract class WindowEvent extends Event
{
    public function __construct(Window $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
