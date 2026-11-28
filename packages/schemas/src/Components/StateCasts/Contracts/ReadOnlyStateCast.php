<?php

namespace Filament\Schemas\Components\StateCasts\Contracts;

/**
 * `set()` must return its input unchanged without side effects. Hydration may skip calling it.
 */
interface ReadOnlyStateCast extends StateCast {}
