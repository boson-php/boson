<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

interface DataRetrieverInterface extends
    SyncDataRetrieverInterface,
    AsyncDataRetrieverInterface {}
