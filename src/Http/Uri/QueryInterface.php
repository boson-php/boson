<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * @template-extends \Traversable<non-empty-string, string>
 * @template-extends \ArrayAccess<non-empty-string, list<string>>
 */
interface QueryInterface extends
    StringValueObjectInterface,
    \ArrayAccess,
    \Traversable,
    \Countable
{
    /**
     * @var non-empty-string
     */
    public const string KV_DELIMITER = '=';

    /**
     * @var non-empty-string
     */
    public const string SEGMENT_DELIMITER = '&';
}
