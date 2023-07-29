<?php

namespace Filament\Panel\Concerns;

use Exception;
use Filament\Contracts\Plugin;

trait HasPlugins
{
    /**
     * @var array<string, Plugin>
     */
    protected array $plugins = [];

    public function plugin(Plugin $plugin): static
    {
        $plugin->register($this);
        $this->plugins[$plugin->getId()] = $plugin;

        return $this;
    }

    /**
     * @param  array<Plugin>  $plugins
     */
    public function plugins(array $plugins): static
    {
        foreach ($plugins as $plugin) {
            $this->plugin($plugin);
        }

        return $this;
    }

    /**
     * @return array<string, Plugin>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getPlugin(string $id): Plugin
    {
        return $this->getPlugins()[$id] ?? throw new Exception("Plugin [{$id}] is not registered for panel [{$this->getId()}].");
    }

    public function hasPlugin(string $id): bool
    {
        return array_key_exists($id, $this->getPlugins());
    }
}
