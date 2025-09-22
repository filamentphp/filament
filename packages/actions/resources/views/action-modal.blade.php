@php
    use Filament\Actions\View\ActionsRenderHook;
    use Filament\Support\Facades\FilamentView;

    $actionModalAlignment = $action->getModalAlignment();
    $actionIsModalAutofocused = $action->isModalAutofocused();
    $actionHasModalCloseButton = $action->hasModalCloseButton();
    $actionIsModalClosedByClickingAway = $action->isModalClosedByClickingAway();
    $actionIsModalClosedByEscaping = $action->isModalClosedByEscaping();
    $actionModalDescription = $action->getModalDescription();
    $actionExtraModalWindowAttributeBag = $action->getExtraModalWindowAttributeBag();
    $actionModalFooterActions = $action->getVisibleModalFooterActions();
    $actionModalFooterActionsAlignment = $action->getModalFooterActionsAlignment();
    $actionModalHeading = $action->getModalHeading();
    $actionModalIcon = $action->getModalIcon();
    $actionModalIconColor = $action->getModalIconColor();
    $actionModalId = "fi-{$this->getId()}-action-{$action->getNestingIndex()}";
    $actionIsModalSlideOver = $action->isModalSlideOver();
    $actionIsModalFooterSticky = $action->isModalFooterSticky();
    $actionIsModalHeaderSticky = $action->isModalHeaderSticky();
    $actionModalWidth = $action->getModalWidth();
    $actionLivewireCallMountedActionName = $action->hasFormWrapper() ? $action->getLivewireCallMountedActionName() : null;
    $actionModalWireKey = "{$this->getId()}.actions.{$action->getName()}.modal";
@endphp

<x-filament::modal
    :alignment="$actionModalAlignment"
    :autofocus="$actionIsModalAutofocused"
    :close-button="$actionHasModalCloseButton"
    :close-by-clicking-away="$actionIsModalClosedByClickingAway"
    :close-by-escaping="$actionIsModalClosedByEscaping"
    :description="$actionModalDescription"
    :extra-modal-window-attribute-bag="$actionExtraModalWindowAttributeBag"
    :footer-actions="$actionModalFooterActions"
    :footer-actions-alignment="$actionModalFooterActionsAlignment"
    :heading="$actionModalHeading"
    :icon="$actionModalIcon"
    :icon-color="$actionModalIconColor"
    :id="$actionModalId"
    :slide-over="$actionIsModalSlideOver"
    :sticky-footer="$actionIsModalFooterSticky"
    :sticky-header="$actionIsModalHeaderSticky"
    :width="$actionModalWidth"
    :wire:key="$actionModalWireKey"
    :wire:submit.prevent="$actionLivewireCallMountedActionName"
    :x-on:modal-closed="'if ($event.detail.id === ' . \Illuminate\Support\Js::from($actionModalId) . ') $wire.unmountAction(false)'"
>
    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_BEFORE, scopes: static::class, data: ['action' => $action]) }}

    {{ $action->getModalContent() }}

    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_AFTER, scopes: static::class, data: ['action' => $action]) }}

    @if ($this->mountedActionHasSchema(mountedAction: $action))
        {{ FilamentView::renderHook(ActionsRenderHook::MODAL_SCHEMA_BEFORE, scopes: static::class, data: ['action' => $action]) }}

        {{ $this->getMountedActionSchema(mountedAction: $action) }}

        {{ FilamentView::renderHook(ActionsRenderHook::MODAL_SCHEMA_AFTER, scopes: static::class, data: ['action' => $action]) }}
    @endif

    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_FOOTER_BEFORE, scopes: static::class, data: ['action' => $action]) }}

    {{ $action->getModalContentFooter() }}

    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_FOOTER_AFTER, scopes: static::class, data: ['action' => $action]) }}
</x-filament::modal>
