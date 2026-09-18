<?php

namespace Filament\Tests\Fixtures\Livewire;

/**
 * A second table class sharing column names with `PostsColumnManagerTable`, so two column
 * managers with identically named columns can render on the same page independently.
 */
class SecondPostsColumnManagerTable extends PostsColumnManagerTable {}
