<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag;
use Filament\Support\View\SupportIconAlias;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_icon_html;

trait CanGeneratePaginationHtml
{
    use CanGenerateButtonHtml;
    use CanGenerateIconButtonHtml;
    use CanGenerateInputHtml;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  string | array<string> | null  $iconAlias
     * @param  array<string>  $loadingTargets
     */
    public function generatePaginationItemHtml(
        ?BaseComponentAttributeBag $attributes = null,
        bool $active = false,
        string | Htmlable | null $ariaLabel = null,
        bool $disabled = false,
        string | BackedEnum | Htmlable | null $icon = null,
        string | array | null $iconAlias = null,
        string | int | Htmlable | null $label = null,
        array $loadingTargets = [],
    ): string {
        $attributes ??= new ComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.pagination.item')) {
            if ($attributes->has('wire:key')) {
                $this->setRenderKey($attributes->get('wire:key'));
            }

            return view('filament::components.pagination.item', [...get_defined_vars(), 'slot' => new ComponentSlot])->render();
        }

        $wireClickAction = $attributes->whereStartsWith('wire:click')->first();
        $hasLoadingState = filled($wireClickAction) && (! $disabled);

        ob_start(); ?>
        <li <?= $attributes->whereDoesntStartWith('wire:click')->class(['fi-pagination-item', 'fi-disabled' => $disabled, 'fi-active' => $active]) ?>>
            <button
                <?php if (filled($ariaLabel)) { ?> aria-label="<?= e($ariaLabel) ?>" <?php } ?>
                <?php if ($active) { ?> aria-current="page" <?php } ?>
                <?php if ($disabled) { ?> aria-hidden="true" disabled <?php } ?>
                data-prevent-disabled-clicks type="button"
                <?= $attributes->whereStartsWith('wire:click')->merge([
                    'wire:loading.attr' => $hasLoadingState ? 'aria-disabled' : null,
                    'wire:loading.class' => $hasLoadingState ? 'fi-disabled' : null,
                    'wire:target' => $hasLoadingState ? (implode(',', $loadingTargets) ?: $wireClickAction) : null,
                ], escape: false)->class(['fi-pagination-item-btn']) ?>
            >
                <?= $this->generateMorphMarkerHtml() ?>
                <?php if ($icon || $iconAlias) {
                    echo e(generate_icon_html($icon, $iconAlias, attributes: (new ComponentAttributeBag)->merge(['aria-hidden' => 'true'], escape: false)->class(['fi-pagination-item-icon'])));
                } ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
                <?php if (filled($label)) { ?><span class="fi-pagination-item-label"><?= e(is_numeric($label) ? Number::format($label) : $label) ?></span><?php } ?>
            </button>
        </li>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  (Paginator & AbstractPaginator) | \Illuminate\Pagination\CursorPaginator  $paginator
     * @param  array<int | string>  $pageOptions
     * @param  array<string>  $loadingTargets
     */
    public function generatePaginationHtml(
        Paginator | CursorPaginator $paginator,
        string $livewireId,
        ?BaseComponentAttributeBag $attributes = null,
        string $currentPageOptionProperty = 'tableRecordsPerPage',
        bool $extremeLinks = false,
        array $pageOptions = [],
        array $loadingTargets = [],
        bool $isRtl = false,
    ): string {
        $attributes ??= new ComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.pagination.index')) {
            if ($attributes->has('wire:key')) {
                $this->setRenderKey($attributes->get('wire:key'));
            }

            return view('filament::components.pagination.index', [...get_defined_vars(), 'slot' => new ComponentSlot])->render();
        }

        $isSimple = ! $paginator instanceof LengthAwarePaginator;
        $loadingTargets = array_unique([...$loadingTargets, 'previousPage', 'gotoPage', 'setPage', 'nextPage', 'paginators', $currentPageOptionProperty]);
        $wireLoadingTargets = implode(',', $loadingTargets);
        $onFirstPage = $paginator->onFirstPage();
        $hasMorePages = $paginator->hasMorePages();
        $currentPage = $isSimple ? null : $paginator->currentPage();
        $pageName = $paginator instanceof CursorPaginator ? $paginator->getCursorName() : $paginator->getPageName();
        $previousAction = $paginator instanceof CursorPaginator
            ? ((! $onFirstPage) ? "setPage('{$paginator->previousCursor()->encode()}', '{$pageName}')" : null)
            : "previousPage('{$pageName}')";
        $nextAction = $paginator instanceof CursorPaginator
            ? ($hasMorePages ? "setPage('{$paginator->nextCursor()->encode()}', '{$pageName}')" : null)
            : "nextPage('{$pageName}')";

        ob_start(); ?>
        <nav aria-label="<?= e(__('filament::components/pagination.label')) ?>" <?= $attributes->class(['fi-pagination', 'fi-simple' => $isSimple]) ?>>
            <?= $this->generateMorphMarkerHtml() ?>
            <?php if (! $onFirstPage) {
                echo $this->generateButtonHtml(label: new ComponentSlot(e(__('filament::components/pagination.actions.previous.label'))), attributes: new ComponentAttributeBag(['rel' => 'prev', 'wire:click' => e($previousAction), 'wire:key' => e($livewireId . '.pagination.previous'), 'wire:target' => e($wireLoadingTargets), 'class' => 'fi-pagination-previous-btn']), color: 'gray', shouldCheckForPublishedView: true);
            } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
            <?= $this->generateMorphMarkerHtml() ?>
            <?php if (! $isSimple) { ?>
                <span class="fi-pagination-overview"><?= e(trans_choice('filament::components/pagination.overview', $paginator->total(), ['first' => Number::format($paginator->firstItem() ?? 0), 'last' => Number::format($paginator->lastItem() ?? 0), 'total' => Number::format($paginator->total())])) ?></span>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
            <?= $this->generateMorphMarkerHtml() ?>
            <?php if (count($pageOptions) > 1) { ?>
                <div class="fi-pagination-records-per-page-select-ctn">
                    <?php ob_start();
                foreach ($this->iterateRenderLoop($pageOptions) as $option) { ?><option value="<?= e($option) ?>"><?= e($option === 'all' ? __('filament::components/pagination.fields.records_per_page.options.all') : $option) ?></option><?php } $optionsHtml = ob_get_clean(); ?>
                    <label class="fi-pagination-records-per-page-select fi-compact">
                        <?= $this->generateInputWrapperHtml(slot: new HtmlString($this->generateInputSelectHtml(slot: new ComponentSlot($optionsHtml), attributes: new ComponentAttributeBag(['wire:model.live' => e($currentPageOptionProperty)])))) ?>
                        <span class="fi-sr-only"><?= e(__('filament::components/pagination.fields.records_per_page.label')) ?></span>
                    </label>
                    <label class="fi-pagination-records-per-page-select">
                        <?= $this->generateInputWrapperHtml(slot: new HtmlString($this->generateInputSelectHtml(slot: new ComponentSlot($optionsHtml), attributes: new ComponentAttributeBag(['wire:model.live' => e($currentPageOptionProperty)]))), prefix: __('filament::components/pagination.fields.records_per_page.label')) ?>
                    </label>
                </div>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
            <?= $this->generateMorphMarkerHtml() ?>
            <?php if ($hasMorePages) {
                echo $this->generateButtonHtml(label: new ComponentSlot(e(__('filament::components/pagination.actions.next.label'))), attributes: new ComponentAttributeBag(['rel' => 'next', 'wire:click' => e($nextAction), 'wire:key' => e($livewireId . '.pagination.next'), 'wire:target' => e($wireLoadingTargets), 'class' => 'fi-pagination-next-btn']), color: 'gray', shouldCheckForPublishedView: true);
            } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
            <?php if ((! $isSimple) && $paginator->hasPages()) { ?>
                <ol class="fi-pagination-items">
                    <?php if (! $onFirstPage) {
                        if ($extremeLinks) {
                            echo $this->generatePaginationItemHtml(attributes: new ComponentAttributeBag(['rel' => 'first', 'wire:click' => e("gotoPage(1, '{$pageName}')"), 'wire:key' => e($livewireId . '.pagination.first')]), ariaLabel: __('filament::components/pagination.actions.first.label'), icon: $isRtl ? Heroicon::ChevronDoubleRight : Heroicon::ChevronDoubleLeft, iconAlias: $isRtl ? SupportIconAlias::PAGINATION_FIRST_BUTTON_RTL : SupportIconAlias::PAGINATION_FIRST_BUTTON, loadingTargets: $loadingTargets);
                        }
                        echo $this->generatePaginationItemHtml(attributes: new ComponentAttributeBag(['rel' => 'prev', 'wire:click' => e($previousAction), 'wire:key' => e($livewireId . '.pagination.previous')]), ariaLabel: __('filament::components/pagination.actions.previous.label'), icon: $isRtl ? Heroicon::ChevronRight : Heroicon::ChevronLeft, iconAlias: $isRtl ? SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL : SupportIconAlias::PAGINATION_PREVIOUS_BUTTON, loadingTargets: $loadingTargets);
                    }
                foreach ($this->iterateRenderLoop(data_get($paginator->render(), 'elements')) as $element) {
                    if (is_string($element)) {
                        echo $this->generatePaginationItemHtml(disabled: true, label: $element);
                    }
                    if (is_array($element)) {
                        foreach ($this->iterateRenderLoop($element) as $page => $url) {
                            echo $this->generatePaginationItemHtml(attributes: new ComponentAttributeBag(['wire:click' => e("gotoPage({$page}, '{$pageName}')"), 'wire:key' => e("{$livewireId}.pagination.{$pageName}.{$page}")]), active: $page === $currentPage, ariaLabel: trans_choice('filament::components/pagination.actions.go_to_page.label', $page, ['page' => Number::format($page)]), label: $page, loadingTargets: $loadingTargets);
                        }
                    }
                }
                if ($hasMorePages) {
                    echo $this->generatePaginationItemHtml(attributes: new ComponentAttributeBag(['rel' => 'next', 'wire:click' => e($nextAction), 'wire:key' => e($livewireId . '.pagination.next')]), ariaLabel: __('filament::components/pagination.actions.next.label'), icon: $isRtl ? Heroicon::ChevronLeft : Heroicon::ChevronRight, iconAlias: $isRtl ? SupportIconAlias::PAGINATION_NEXT_BUTTON_RTL : SupportIconAlias::PAGINATION_NEXT_BUTTON, loadingTargets: $loadingTargets);
                    if ($extremeLinks) {
                        echo $this->generatePaginationItemHtml(attributes: new ComponentAttributeBag(['rel' => 'last', 'wire:click' => e("gotoPage({$paginator->lastPage()}, '{$pageName}')"), 'wire:key' => e($livewireId . '.pagination.last')]), ariaLabel: __('filament::components/pagination.actions.last.label'), icon: $isRtl ? Heroicon::ChevronDoubleLeft : Heroicon::ChevronDoubleRight, iconAlias: $isRtl ? SupportIconAlias::PAGINATION_LAST_BUTTON_RTL : SupportIconAlias::PAGINATION_LAST_BUTTON, loadingTargets: $loadingTargets);
                    }
                } ?>
                </ol>
            <?php } ?>
        </nav>
        <?php return ob_get_clean();
    }
}
