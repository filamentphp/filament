<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Field extends Component
{
    /**
     * @var string
     */
    protected string $bindingAttribute = 'wire:model.defer';

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var array
     */
    protected array $extraAttributes = [];

    /**
     * @var string
     */
    protected string $helpMessage = '';

    /**
     * @var string
     */
    protected string $hint = '';

    /**
     * @var bool
     */
    protected bool $isDisabled = false;

    /**
     * @var bool
     */
    protected bool $isRequired = false;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array
     */
    protected array $rules = [];

    /**
     * @var string
     */
    protected string $validationAttribute;

    /**
     * Field constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function addRules($rules) : self
    {
        $this->configure(function () use ($rules) {
            if (! is_array($rules)) {
                $rules = [$this->getName() => $rules];
            }

            foreach ($rules as $field => $conditionsToAdd) {
                if (is_numeric($field)) {
                    $field = $this->getName();
                }

                if (! is_array($conditionsToAdd)) {
                    $conditionsToAdd = explode('|', $conditionsToAdd);
                }

                $this->rules[$field] = collect($this->getRules($field) ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToAdd) {
                        if (! is_string($originalCondition)) {
                            return true;
                        }

                        $conditionsToAdd = collect($conditionsToAdd);

                        if ($conditionsToAdd->contains($originalCondition)) {
                            return false;
                        }

                        if (! Str::of($originalCondition)->contains(':')) {
                            return true;
                        }

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToAdd->contains(function ($conditionToAdd) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToAdd)->before(':');
                        });
                    })
                    ->push(...$conditionsToAdd)
                    ->toArray();
            }
        });

        return $this;
    }

    /**
     * @param string $bindingAttribute
     *
     * @return $this
     */
    public function bindingAttribute(string $bindingAttribute) : self
    {
        $this->configure(function () use ($bindingAttribute) {
            $this->bindingAttribute = $bindingAttribute;
        });

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function default($value) : self
    {
        $this->configure(function () use ($value) {
            $this->defaultValue = $value;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function dependable() : self
    {
        $this->configure(function () {
            $this->bindingAttribute('wire:model');
        });

        return $this;
    }

    /**
     * @param bool $disabled
     *
     * @return $this
     */
    public function disabled($disabled = true) : self
    {
        $this->configure(function () use ($disabled) {
            $this->isDisabled = $disabled;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function enabled() : self
    {
        $this->configure(function () {
            $this->isDisabled = false;
        });

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function extraAttributes(array $attributes) : self
    {
        $this->configure(function () use ($attributes) {
            $this->extraAttributes = $attributes;
        });

        return $this;
    }

    /**
     * @return string
     */
    public function getBindingAttribute() : string
    {
        return $this->bindingAttribute;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    public function getExtraAttributes() : array
    {
        return $this->extraAttributes;
    }

    /**
     * @return string
     */
    public function getHelpMessage() : ?string
    {
        return $this->helpMessage;
    }

    /**
     * @return string
     */
    public function getHint() : string
    {
        return $this->hint;
    }

    /**
     * @return string
     */
    public function getId()
    {
        if ($this->id === null) {
            return (string) Str::of($this->getName())
                ->replace('.', '-')
                ->slug();
        }

        return parent::getId();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValidationAttribute() : string
    {
        if ($this->validationAttribute === null) {
            return Str::lower($this->getLabel());
        }

        return $this->validationAttribute;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getLivewire()->getPropertyValue($this->getName());
    }

    /**
     * @param string|null $message
     *
     * @return $this
     */
    public function helpMessage(?string $message) : self
    {
        $this->configure(function () use ($message) {
            $this->helpMessage = $message;
        });

        return $this;
    }

    /**
     * @param string|null $hint
     *
     * @return $this
     */
    public function hint(?string $hint)
    {
        $this->configure(function () use ($hint) {
            $this->hint = $hint;
        });

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled() : bool
    {
        return $this->isDisabled;
    }

    /**
     * @return bool
     */
    public function isRequired() : bool
    {
        return $this->isRequired;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public static function make($name)
    {
        return new static($name);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function name(string $name) : self
    {
        $this->name = $name;

        return $this->addRules([$this->getName() => ['nullable']]);
    }

    /**
     * @return $this
     */
    public function nullable() : self
    {
        $this->configure(function () {
            $this->required = false;

            $this->removeRules([$this->getName() => ['required']]);
            $this->addRules([$this->getName() => ['nullable']]);
        });

        return $this;
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function removeRules(array $rules) : self
    {
        $this->configure(function () use ($rules) {
            if (! is_array($rules)) {
                $rules = [$this->getName() => $rules];
            }

            foreach ($rules as $field => $conditionsToRemove) {
                if (is_numeric($field)) {
                    $field = $this->getName();
                }

                if (! is_array($conditionsToRemove)) {
                    $conditionsToRemove = explode('|', $conditionsToRemove);
                }

                if (empty($conditionsToRemove)) {
                    unset($this->rules[$field]);

                    return;
                }

                $this->rules[$field] = collect($this->getRules($field) ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToRemove) {
                        if (! is_string($originalCondition)) {
                            return true;
                        }

                        $conditionsToRemove = collect($conditionsToRemove);

                        if ($conditionsToRemove->contains($originalCondition)) {
                            return false;
                        }

                        if (! Str::of($originalCondition)->contains(':')) {
                            return true;
                        }

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToRemove->contains(function ($conditionToRemove) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToRemove)->before(':');
                        });
                    })
                    ->toArray();
            }
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function required() : self
    {
        $this->configure(function () {
            $this->isRequired = true;

            $this->removeRules([$this->getName() => ['nullable']]);
            $this->addRules([$this->getName() => ['required']]);
        });

        return $this;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function requiredWith($field)
    {
        $this->configure(function () use ($field) {
            $this->isRequired = false;

            $this->removeRules([$this->getName() => ['nullable', 'required']]);
            $this->addRules([$this->getName() => ["required_with:$field"]]);
        });

        return $this;
    }

    /**
     * @param $conditions
     *
     * @return $this
     */
    public function rules($conditions) : self
    {
        $this->configure(function () use ($conditions) {
            $this->addRules([$this->getName() => $conditions]);
        });

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function validationAttribute(string $attribute) : self
    {
        $this->configure(function () use ($attribute) {
            $this->validationAttribute = $attribute;
        });

        return $this;
    }
}
