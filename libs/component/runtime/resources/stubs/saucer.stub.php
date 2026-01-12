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
         * @var null|\Closure(CData, CData):void
         */
        public null|\Closure $onDomReady;

        /**
         * @var null|\Closure(CData, string, CData):void
         */
        public null|\Closure $onNavigated;

        /**
         * @var null|\Closure(CData, CData, CData):void
         */
        public null|\Closure $onNavigating;

        /**
         * @var null|\Closure(CData, CData, CData):void
         */
        public null|\Closure $onFaviconChanged;

        /**
         * @var null|\Closure(CData, string, CData):void
         */
        public null|\Closure $onTitleChanged;

        /**
         * @var null|\Closure(CData, State::SAUCER_STATE_*, CData):void
         */
        public null|\Closure $onLoad;

        /**
         * @var null|\Closure(CData, string, int<0, max>, CData):void
         */
        public null|\Closure $onMessage;
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
         * @var null|\Closure(CData, bool): void
         */
        public null|\Closure $onDecorated;

        /**
         * @var null|\Closure(CData, bool): void
         */
        public null|\Closure $onMaximize;

        /**
         * @var null|\Closure(CData, bool): void
         */
        public null|\Closure $onMinimize;

        /**
         * @var null|\Closure(CData): Policy::SAUCER_POLICY_*
         */
        public null|\Closure $onClosing;

        /**
         * @var null|\Closure(CData): void
         */
        public null|\Closure $onClosed;

        /**
         * @var null|\Closure(CData, int<0, 2147483647>, int<0, 2147483647>): void
         */
        public null|\Closure $onResize;

        /**
         * @var null|\Closure(CData, bool): void
         */
        public null|\Closure $onFocus;
    }

}
