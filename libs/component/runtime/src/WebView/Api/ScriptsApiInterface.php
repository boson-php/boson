<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\Scripts\MutableScriptsSetInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;

interface ScriptsApiInterface extends
    MutableScriptsSetInterface,
    ScriptEvaluatorInterface {}
