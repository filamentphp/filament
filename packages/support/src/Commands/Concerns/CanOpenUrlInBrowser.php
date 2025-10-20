<?php

namespace Filament\Support\Commands\Concerns;

trait CanOpenUrlInBrowser
{
    public function openUrlInBrowser(string $url): void
    {
        $result = -1;

        if (PHP_OS_FAMILY === 'Darwin') {
            exec('open "' . $url . '"', result_code: $result);
        }
        if (PHP_OS_FAMILY === 'Linux') {
            exec('xdg-open "' . $url . '"', result_code: $result);
        }
        if (PHP_OS_FAMILY === 'Windows') {
            exec('start "" "' . $url . '"', result_code: $result);
        }

        if ($result !== 0) {
            $this->components->error('An error occurred while trying to open the page in your browser.');
            $this->output->writeln('  <comment>Please open the following URL in your browser:</>');
            $this->output->writeln('  <href="' . $url . '">' . $url . '</>');
        }
    }
}
