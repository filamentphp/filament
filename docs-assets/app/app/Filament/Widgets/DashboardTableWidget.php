<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DashboardTableWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query()->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->label('Order')
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Customer'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Priority')
                    ->boolean(),
            ])
            ->paginated(false);
    }
}
