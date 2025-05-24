<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Dispatcher\Intention;
use Boson\Window\Window;

/**
 * @template-extends Intention<Window>
 */
abstract class WindowIntention extends Intention
{
    public function __construct(Window $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
