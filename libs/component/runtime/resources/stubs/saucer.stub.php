<?php

namespace Boson\Internal\WebView {

    use Boson\Component\Saucer\State;
    use FFI\CData;

    /**
     * @internal this is an INTERNAL STRUCT for PHPStan only, please do not use it in your code
     * @psalm-internal Boson\Internal\WebView
     *
     * @seal-properties
     * @seal-methods
     */
    final class CSaucerWebViewEventsStruct extends CData
    {
        /**
         * @var \Closure(CData, CData):void
         */
        public \Closure $onDomReady;

        /**
         * @var \Closure(CData, string, CData):void
         */
        public \Closure $onNavigated;

        /**
         * @var \Closure(CData, CData, CData):void
         */
        public \Closure $onNavigating;

        /**
         * @var \Closure(CData, CData, CData):void
         */
        public \Closure $onFaviconChanged;

        /**
         * @var \Closure(CData, string, CData):void
         */
        public \Closure $onTitleChanged;

        /**
         * @var \Closure(CData, State::SAUCER_STATE_*, CData):void
         */
        public \Closure $onLoad;

        /**
         * @var \Closure(CData, string, int<0, max>, CData):void
         */
        public \Closure $onMessage;
    }

}


namespace Boson\Internal\Window {

    use Boson\Component\Saucer\Policy;
    use FFI\CData;

    /**
     * @internal this is an INTERNAL STRUCT for PHPStan only, please do not use it in your code
     * @psalm-internal Boson\Internal\Window
     *
     * @seal-properties
     * @seal-methods
     */
    final class CSaucerWindowEventsStruct extends CData
    {
        /**
         * @var \Closure(CData, bool): void
         */
        public \Closure $onDecorated;

        /**
         * @var \Closure(CData, bool): void
         */
        public \Closure $onMaximize;

        /**
         * @var \Closure(CData, bool): void
         */
        public \Closure $onMinimize;

        /**
         * @var \Closure(CData): Policy::SAUCER_POLICY_*
         */
        public \Closure $onClosing;

        /**
         * @var \Closure(CData): void
         */
        public \Closure $onClosed;

        /**
         * @var \Closure(CData, int<0, 2147483647>, int<0, 2147483647>): void
         */
        public \Closure $onResize;

        /**
         * @var \Closure(CData, bool): void
         */
        public \Closure $onFocus;
    }

}
