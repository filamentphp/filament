<?php

namespace Filament\Resources\RelationManagers;

use Closure;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasBadgeTooltip;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Illuminate\Database\Eloquent\Model;

class RelationGroup extends Component
{
    use HasBadgeTooltip;
    use HasIcon;
    use HasIconPosition;

    protected string | Closure | null $badge = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    protected ?Model $ownerRecord = null;

    protected ?string $pageClass = null;

    protected ?Closure $modifyTabUsing = null;

    /**
     * @param  array<class-string<RelationManager> | RelationManagerConfiguration>  $managers
     */
    public function __construct(
        protected string | Closure $label,
        protected array | Closure $managers,
    ) {}

    /**
     * @param  array<class-string<RelationManager> | RelationManagerConfiguration>  $managers
     */
    public static function make(string | Closure $label, array | Closure $managers): static
    {
        $static = app(static::class, ['label' => $label, 'managers' => $managers]);
        $static->configure();

        return $static;
    }

    public function ownerRecord(?Model $record): static
    {
        $this->ownerRecord = $record;

        return $this;
    }

    public function pageClass(?string $class): static
    {
        $this->pageClass = $class;

        return $this;
    }

    public function badge(string | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function badgeColor(string | array | Closure | null $color): static
    {
        $this->badgeColor = $color;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label);
    }

    public function tab(?Closure $callback): static
    {
        $this->modifyTabUsing = $callback;

        return $this;
    }

    /**
     * @return array<class-string<RelationManager> | RelationManagerConfiguration>
     */
    public function getManagers(): array
    {
        $ownerRecord = $this->getOwnerRecord();
        $pageClass = $this->getPageClass();

        if (! ($ownerRecord && $pageClass)) {
            return $this->managers;
        }

        return array_filter(
            $this->managers,
            fn (string | RelationManagerConfiguration $manager): bool => $this->normalizeRelationManagerClass($manager)::canViewForRecord($ownerRecord, $pageClass),
        );
    }

    /**
     * @param  class-string<RelationManager> | RelationManagerConfiguration  $manager
     * @return class-string<RelationManager>
     */
    protected function normalizeRelationManagerClass(string | RelationManagerConfiguration $manager): string
    {
        if ($manager instanceof RelationManagerConfiguration) {
            return $manager->relationManager;
        }

        return $manager;
    }

    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    /**
     * @return string | array<string> | null
     */
    public function getBadgeColor(): string | array | null
    {
        return $this->evaluate($this->badgeColor);
    }

    public function getOwnerRecord(): ?Model
    {
        return $this->ownerRecord;
    }

    public function getPageClass(): ?string
    {
        return $this->pageClass;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'ownerRecord' => [$this->getOwnerRecord()],
            'pageClass' => [$this->getPageClass()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $ownerRecord = is_a($parameterType, Model::class, allow_string: true) ? $this->getOwnerRecord() : null;

        if (! $ownerRecord) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $ownerRecord::class => [$ownerRecord],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function getTabComponent(): Tab
    {
        $tab = Tab::make($this->getLabel())
            ->badge($this->getBadge())
            ->badgeColor($this->getBadgeColor())
            ->badgeTooltip($this->getBadgeTooltip())
            ->icon($this->getIcon())
            ->iconPosition($this->getIconPosition());

        return $this->evaluate($this->modifyTabUsing, [
            'tab' => $tab,
        ]) ?? $tab;
    }
}
