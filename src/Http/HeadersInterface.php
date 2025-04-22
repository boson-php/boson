<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * @template-extends \Traversable<non-empty-lowercase-string, string>
 * @template-extends \ArrayAccess<non-empty-lowercase-string, list<string>>
 */
interface HeadersInterface extends \Traversable, \ArrayAccess, \Countable {}
