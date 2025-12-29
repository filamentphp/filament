<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Support\Commands\Concerns\HasPanel;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-user', aliases: [
    'filament:make-user',
    'filament:user',
])]
class MakeUserCommand extends Command
{
    use HasPanel;

    protected $description = 'Create a new Filament user';

    protected $name = 'make:filament-user';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-user',
        'filament:user',
    ];

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'name',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The name of the user',
            ),
            new InputOption(
                name: 'email',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'A valid and unique email address',
            ),
            new InputOption(
                name: 'password',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The password for the user (min. 8 characters)',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the user in',
            ),
        ];
    }

    /**
     * @var array{'name': string | null, 'email': string | null, 'password': string | null}
     */
    protected array $options;

    public function handle(): int
    {
        $this->configurePanel(question: 'Which panel would you like to create this user in?');

        $this->options = $this->options();

        if (! Filament::getCurrentOrDefaultPanel()) {
            $this->error('Filament has not been installed yet: php artisan filament:install --panels');

            return static::FAILURE;
        }

        $user = $this->createUser();
        $this->sendSuccessMessage($user);

        return static::SUCCESS;
    }

    /**
     * @return array{'name': string, 'email': string, 'password': string}
     */
    protected function getUserData(): array
    {
        return [
            'name' => $this->options['name'] ?? text(
                label: 'Name',
                required: true,
            ),

            'email' => $this->options['email'] ?? text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::query()->where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),

            'password' => Hash::make($this->options['password'] ?? password(
                label: 'Password',
                required: true,
            )),
        ];
    }

    protected function createUser(): Model & Authenticatable
    {
        /** @var Model & Authenticatable $user */
        $user = static::getUserModel()::query()->create($this->getUserData());

        return $user;
    }

    protected function sendSuccessMessage(Model & Authenticatable $user): void
    {
        $loginUrl = $this->panel->getLoginUrl();

        $this->components->info('Success! ' . ($user->getAttribute('email') ?? $user->getAttribute('username') ?? 'You') . " may now log in at {$loginUrl}");
    }

    protected function getAuthGuard(): Guard
    {
        return $this->panel->auth();
    }

    protected function getUserProvider(): UserProvider
    {
        return $this->getAuthGuard()->getProvider();
    }

    /**
     * @return class-string<Model & Authenticatable>
     */
    protected function getUserModel(): string
    {
        /** @var EloquentUserProvider $provider */
        $provider = $this->getUserProvider();

        return $provider->getModel();
    }
}
