<input
    type="hidden"
    {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
    {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
    {!! $formComponent->isRequired() ? 'required' : null !!}
    {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
/>
