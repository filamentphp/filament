<?php

namespace Filament\Support\Commands\Concerns;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

trait CanAskForRelatedResource
{
    /**
     * @return ?class-string
     */
    protected function askForRelatedResource(): ?string
    {
        info('Filament can link this to an existing resource, which will open the resource\'s pages instead of modals when links are clicked. It will also inherit the resource\'s configuration.');

        if (! confirm(
            label: 'Do you want to do this?',
            default: false,
        )) {
            return null;
        }

        $clusterFqn = $this->askForCluster(
            initialQuestion: 'Is the resource in a cluster?',
            question: 'Which cluster is the resource in?',
        );

        if (filled($clusterFqn)) {
            [$resourcesNamespace] = $this->getClusterResourcesLocation($clusterFqn);
        } else {
            [$resourcesNamespace] = $this->getResourcesLocation(
                question: 'Which namespace would you like to search for resources in?',
            );
        }

        return $this->askForResource(
            question: 'Which resource do you want to use?',
            resourcesNamespace: $resourcesNamespace,
        );
    }
}
