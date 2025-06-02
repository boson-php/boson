<?php

Phar::mapPhar('{name}.phar');
Phar::interceptFileFuncs();

require 'phar://{name}.phar/{entrypoint}';

__HALT_COMPILER();
