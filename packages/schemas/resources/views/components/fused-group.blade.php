@php
    use Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
    use Filament\Forms\Components\Field;

    $fieldWrapperView = $getFieldWrapperView();

    $errorMessages = null;
    $errorMessage = null;

    foreach ($getChildComponentContainer()->getComponents() as $childComponent) {
        if (! ($childComponent instanceof Field)) {
            continue;
        }

        $statePath = $childComponent->getStatePath();

        if (blank($statePath)) {
            continue;
        }

        if ($errors->has($statePath)) {
            if ($childComponent->shouldShowAllValidationMessages()) {
                $errorMessages = $errors->get($statePath);
                $shouldShowAllValidationMessages = true;
            } else {
                $errorMessage = $errors->first($statePath);
            }

            $areHtmlValidationMessagesAllowed = $childComponent->areHtmlValidationMessagesAllowed();

            break;
        }

        if (! ($childComponent instanceof HasNestedRecursiveValidationRules)) {
            continue;
        }

        if ($errors->has("{$statePath}.*")) {
            if ($childComponent->shouldShowAllValidationMessages()) {
                $errorMessages = $errors->get("{$statePath}.*");
                $shouldShowAllValidationMessages = true;
            } else {
                $errorMessage = $errors->first("{$statePath}.*");
            }

            $areHtmlValidationMessagesAllowed = $childComponent->areHtmlValidationMessagesAllowed();

            break;
        }
    }
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :error-message="$errorMessage"
    :error-messages="$errorMessages"
    :are-html-error-messages-allowed="$areHtmlValidationMessagesAllowed ?? false"
    :should-show-all-validation-messages="$shouldShowAllValidationMessages ?? false"
    :field="$schemaComponent"
>
    <div
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-sc-fused-group'])
        }}
    >
        {{ $getChildSchema() }}
    </div>
</x-dynamic-component>
