<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

final readonly class FormDataBoundary
{
    /**
     * @var non-empty-string
     */
    public string $segment;

    /**
     * @var non-empty-string
     */
    public string $end;

    /**
     * @param non-empty-string $delimiter
     */
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $value,
    ) {
        $this->segment = '--' . $this->value;
        $this->end = '--' . $this->value . '--';
    }
}
