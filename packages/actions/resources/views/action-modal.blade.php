@php
    use Filament\Actions\View\ActionsRenderHook;
    use Filament\Support\Facades\FilamentView;
    use Illuminate\Support\Js;

    $actionIsModalAlert = $action->isConfirmationRequired();
    $actionModalAlignment = $action->getModalAlignment();
    $actionIsModalAutofocused = $action->isModalAutofocused();
    $actionIsModalClickThrough = $action->isModalClickThrough();
    $actionHasModalCloseButton = $action->hasModalCloseButton();
    $actionIsModalClosedByClickingAway = $action->isModalClosedByClickingAway();
    $actionIsModalClosedByEscaping = $action->isModalClosedByEscaping();
    $actionModalDescription = $action->getModalDescription();
    $actionExtraModalWindowAttributeBag = $action->getExtraModalWindowAttributeBag();
    $actionExtraModalOverlayAttributeBag = $action->getExtraModalOverlayAttributeBag();
    $actionModalFooterActions = $action->getVisibleModalFooterActions();
    $actionModalFooterActionsAlignment = $action->getModalFooterActionsAlignment();
    $actionModalHeading = $action->getModalHeading();
    $actionModalIcon = $action->getModalIcon();
    $actionModalIconColor = $action->getModalIconColor();
    $actionModalId = "fi-{$this->getId()}-action-{$action->getNestingIndex()}";
    $actionIsModalSlideOver = $action->isModalSlideOver();
    $actionModalSlideOverPosition = $action->getModalSlideOverPosition();
    $actionIsModalFooterSticky = $action->isModalFooterSticky();
    $actionIsModalHeaderSticky = $action->isModalHeaderSticky();
    $actionModalWidth = $action->getModalWidth();
    $actionLivewireCallMountedActionName = $action->hasFormWrapper() ? $action->getLivewireCallMountedActionName() : null;
    $actionModalWireKey = "{$this->getId()}.actions.{$action->getName()}.modal";
    $actionModalClosedEventHandler = 'if ($event.detail.id === ' .
        Js::from($actionModalId) .
        ') $wire.unmountAction(' .
        Js::from($action->getParentActionsToCancelOnClose()) .
        ')';
@endphp

<x-filament::modal
    :alert="$actionIsModalAlert"
    :alignment="$actionModalAlignment"
    :autofocus="$actionIsModalAutofocused"
    :click-through="$actionIsModalClickThrough"
    :close-button="$actionHasModalCloseButton"
    :close-by-clicking-away="$actionIsModalClosedByClickingAway"
    :close-by-escaping="$actionIsModalClosedByEscaping"
    :description="$actionModalDescription"
    :focus-trap-returns-focus="false"
    :extra-modal-window-attribute-bag="$actionExtraModalWindowAttributeBag"
    :extra-modal-overlay-attribute-bag="$actionExtraModalOverlayAttributeBag"
    :footer-actions="$actionModalFooterActions"
    :footer-actions-alignment="$actionModalFooterActionsAlignment"
    :heading="$actionModalHeading"
    :icon="$actionModalIcon"
    :icon-color="$actionModalIconColor"
    :id="$actionModalId"
    :slide-over="$actionIsModalSlideOver"
    :slide-over-position="$actionModalSlideOverPosition"
    :sticky-footer="$actionIsModalFooterSticky"
    :sticky-header="$actionIsModalHeaderSticky"
    :width="$actionModalWidth"
    :wire:key="$actionModalWireKey"
    :wire:submit.prevent="$actionLivewireCallMountedActionName"
    :x-on:modal-closed="$actionModalClosedEventHandler"
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
