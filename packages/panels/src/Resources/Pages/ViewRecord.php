<?php

namespace Filament\Resources\Pages;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model = Model
 *
 * @property-read Schema $form
 */
class ViewRecord extends Page
{
    use Concerns\HasRelationManagers {
        getContentTabComponent as getBaseContentTabComponent;
    }
    use Concerns\InteractsWithRecord {
        getRecord as getBaseRecord;
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve(PanelsIconAlias::RESOURCES_PAGES_VIEW_RECORD_NAVIGATION_ITEM)
            ?? Heroicon::OutlinedEye;
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public static function getNavigationLabel(): string
    {
        if (filled(static::$navigationLabel)) {
            return static::$navigationLabel;
        }

        return __('filament-panels::resources/pages/view-record.navigation_label');
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament-panels::resources/pages/view-record.content.tab.label');
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    protected function hasInfolist(): bool
    {
        return (bool) count($this->getSchema('infolist')->getComponents());
    }

    protected function fillForm(): void
    {
        /** @internal Read the DocBlock above the following method. */
        $this->fillFormWithDataAndCallHooks($this->getRecord());
    }

    /**
     * @internal Never override or call this method. If you completely override `fillForm()`, copy the contents of this method into your override.
     *
     * @param  array<string, mixed>  $extraData
     */
    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->attributesToArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string>  $statePaths
     */
    public function refreshFormData(array $statePaths): void
    {
        $this->form->fillPartially(
            $this->mutateFormDataBeforeFill($this->getRecord()->attributesToArray()),
            $statePaths,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function getDefaultActionSchemaResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof CreateAction, $action instanceof EditAction => fn (Schema $schema): Schema => static::getResource()::form($schema->columns(2)),
            $action instanceof ViewAction => fn (Schema $schema): Schema => $this->hasInfolist() ? $schema->components([EmbeddedSchema::make('infolist')]) : $schema->components([EmbeddedSchema::make('form')]),
            default => null,
        };
    }

    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/view-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->disabled()
            ->inlineLabel($this->hasInlineLabels())
            ->model($this->getRecord())
            ->operation('view')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return static::getResource()::form($schema);
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels())
            ->record($this->getRecord());
    }

    public function infolist(Schema $schema): Schema
    {
        return static::getResource()::infolist($schema);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation($parameters) && static::getResource()::canView($parameters['record']);
    }

    public function content(Schema $schema): Schema
    {
        if ($this->hasCombinedRelationManagerTabsWithContent()) {
            return $schema
                ->components([
                    $this->getRelationManagersContentComponent(),
                ]);
        }

        return $schema
            ->components([
                $this->hasInfolist()
                    ? $this->getInfolistContentComponent()
                    : $this->getFormContentComponent(),
                $this->getRelationManagersContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return EmbeddedSchema::make('form');
    }

    public function getInfolistContentComponent(): Component
    {
        return EmbeddedSchema::make('infolist');
    }

    public function getContentTabComponent(): Tab
    {
        return $this->getBaseContentTabComponent()
            ->schema([
                $this->hasInfolist()
                    ? $this->getInfolistContentComponent()
                    : $this->getFormContentComponent(),
            ]);
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-view-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return $this->hasInfolist() ? 'infolist' : parent::getDefaultTestingSchemaName();
    }

    /**
     * @return TModel
     */
    public function getRecord(): Model
    {
        return $this->getBaseRecord();
    }
}
