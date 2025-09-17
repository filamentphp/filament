<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [];
    }
}
