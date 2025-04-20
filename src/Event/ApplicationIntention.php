<?php

declare(strict_types=1);

namespace Boson\Event;

use Boson\Application;
use Boson\Dispatcher\Intention;

/**
 * @template-extends Intention<Application>
 */
abstract class ApplicationIntention extends Intention
{
    public function __construct(Application $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
