<?php

namespace Filament\Context\Concerns;

use Filament\Support\Color;

trait HasColors
{
    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $primaryColor = null;

    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $secondaryColor = null;

    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $grayColor = null;

    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $dangerColor = null;

    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $warningColor = null;

    /**
     * @var array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected ?array $successColor = null;

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string  $color
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string
     */
    public function processColor(array | string $color): array | string
    {
        if (is_string($color) && str_starts_with($color, '#')) {
            return Color::hex($color);
        }

        if (is_string($color) && str_starts_with($color, 'rgb')) {
            return Color::rgb($color);
        }

        return $color;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function primaryColor(array | string | null $color): static
    {
        $this->primaryColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getPrimaryColor(): array
    {
        return $this->primaryColor ?? Color::Amber;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function secondaryColor(array | string | null $color): static
    {
        $this->secondaryColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSecondaryColor(): array
    {
        return $this->secondaryColor ?? Color::Gray;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function grayColor(array | string | null $color): static
    {
        $this->grayColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getGrayColor(): array
    {
        return $this->grayColor ?? Color::Gray;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function dangerColor(array | string | null $color): static
    {
        $this->dangerColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getDangerColor(): array
    {
        return $this->dangerColor ?? Color::Red;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function warningColor(array | string | null $color): static
    {
        $this->warningColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getWarningColor(): array
    {
        return $this->warningColor ?? Color::Amber;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string | null  $color
     */
    public function successColor(array | string | null $color): static
    {
        $this->successColor = $this->processColor($color);

        return $this;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSuccessColor(): array
    {
        return $this->successColor ?? Color::Green;
    }

    /**
     * @return array{
     *     'primary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'secondary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'gray': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'danger': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'warning': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'success': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     * }
     */
    public function getColors(): array
    {
        return [
            'primary' => $this->getPrimaryColor(),
            'secondary' => $this->getSecondaryColor(),
            'gray' => $this->getGrayColor(),
            'danger' => $this->getDangerColor(),
            'warning' => $this->getWarningColor(),
            'success' => $this->getSuccessColor(),
        ];
    }
}
