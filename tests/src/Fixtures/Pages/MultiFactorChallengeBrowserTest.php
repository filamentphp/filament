<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class MultiFactorChallengeBrowserTest extends Page
{
    use RestrictsFileUploadsToSchemaComponents;

    protected string $view = 'pages.multi-factor-challenge-browser-test';

    protected static ?string $title = 'Verify multi-factor authentication';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $isVerified = false;

    public bool $shouldSeparateChallengeSchemaComponents = false;

    public function mount(bool $shouldSeparateChallengeSchemaComponents = false): void
    {
        $this->shouldSeparateChallengeSchemaComponents = $shouldSeparateChallengeSchemaComponents;

        $user = $this->getAuthenticatedUser();
        $multiFactorChallenge = MultiFactorChallenge::make();

        abort_unless($multiFactorChallenge->hasEnabledProviders($user), 403);

        $multiFactorChallenge->beforeChallenge($user);

        $this->multiFactorChallengeForm->fill();
    }

    public function multiFactorChallengeForm(Schema $schema): Schema
    {
        $multiFactorChallenge = MultiFactorChallenge::make();
        $user = $this->getAuthenticatedUser();

        if ($this->shouldSeparateChallengeSchemaComponents) {
            return $schema
                ->components([
                    Group::make(Arr::wrap($multiFactorChallenge->getProviderPickerSchemaComponent($user))),
                    Group::make($multiFactorChallenge->getChallengeSchemaComponents($user)),
                ])
                ->statePath('data');
        }

        return $schema
            ->components($multiFactorChallenge->getSchemaComponents($user))
            ->statePath('data');
    }

    public function verify(): void
    {
        $user = $this->getAuthenticatedUser();
        $multiFactorChallenge = MultiFactorChallenge::make();

        abort_unless($multiFactorChallenge->hasEnabledProviders($user), 403);
        abort_if($multiFactorChallenge->isRateLimited($user), 429);

        $multiFactorChallenge->hitRateLimiter($user);

        $this->multiFactorChallengeForm->getState();

        $this->isVerified = true;
    }

    protected function getAuthenticatedUser(): Authenticatable
    {
        $user = Filament::auth()->user();

        abort_unless($user instanceof Authenticatable, 403);

        return $user;
    }
}
