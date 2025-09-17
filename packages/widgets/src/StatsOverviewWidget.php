<?php

namespace Filament\Widgets;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends Widget implements HasSchemas
{
    use Concerns\CanPoll;
    use InteractsWithSchemas;

    /**
     * @var array<Stat> | null
     */
    protected ?array $cachedStats = null;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = null;

    protected ?string $description = null;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int | array | null $columns = null;

    /**
     * @var view-string
     */
    protected string $view = 'filament-widgets::stats-overview-widget';

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getSectionContentComponent(),
            ]);
    }

    public function getSectionContentComponent(): Component
    {
        return Section::make()
            ->heading($this->getHeading())
            ->description($this->getDescription())
            ->schema($this->getCachedStats())
            ->columns($this->getColumns())
            ->contained(false)
            ->gridContainer();
    }

    /**
     * @return int | array<string, ?int> | null
     */
    protected function getColumns(): int | array | null
    {
        if ($this->columns) {
            return $this->columns;
        }

        $count = count($this->getCachedStats());

        if ($count < 3) {
            return ['@xl' => 3, '!@lg' => 3];
        }

        if (($count % 3) !== 1) {
            return ['@xl' => 3, '!@lg' => 3];
        }

        return ['@xl' => 4, '!@lg' => 4];
    }

    protected function getDescription(): ?string
    {
        return $this->description;
    }

    protected function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * @return array<Stat>
     */
    protected function getCachedStats(): array
    {
        return $this->cachedStats ??= $this->getStats();
    }

    /**
     * @deprecated Use `getStats()` instead.
     *
     * @return array<Stat>
     */
    protected function getCards(): array
    {
        return [];
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return $this->getCards();
    }
}
