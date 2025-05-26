<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard {
    const POSIX = new BuiltinStandard('POSIX');
}

namespace Boson\Component\OsInfo\Family {
    const WINDOWS = new BuiltinFamily('Windows');

    const UNIX = new BuiltinFamily('Unix');

    const LINUX = new BuiltinFamily('Linux', UNIX);

    const BSD = new BuiltinFamily('BSD', UNIX);

    const SOLARIS = new BuiltinFamily('Solaris', BSD);

    const DARWIN = new BuiltinFamily('Darwin', BSD);
}
