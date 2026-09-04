<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Fixed datasets for table demos that need many rows, so that screenshots
 * are identical across generations. Never use factories or Faker here:
 * random data changes the screenshots every time they are generated.
 */
class DemoData
{
    /**
     * @var array<array{0: string, 1: string, 2: string, 3: float, 4: bool}>
     */
    protected const array POSTS = [
        ['Getting Started with the Filament CLI', 'Learn how to scaffold resources, pages, and widgets straight from the command line.', 'published', 8.4, false],
        ['Building a Blog in an Afternoon', 'A practical walkthrough of building a fully-featured blog admin with Filament.', 'published', 9.1, true],
        ['Understanding Schemas', 'Explore how schemas power forms, infolists, and layouts across the framework.', 'published', 8.9, false],
        ['A Deep Dive into Table Filters', 'Build powerful filtering experiences with select, ternary, and custom filters.', 'draft', 7.6, false],
        ['Mastering Repeaters', 'Everything you need to know about managing repeatable form data.', 'published', 8.2, false],
        ['Conditional Form Fields Explained', 'Show, hide, and disable fields reactively based on user input.', 'reviewing', 7.9, false],
        ['Working with Select Relationships', 'Load, search, and create related records directly from a select field.', 'published', 8.6, false],
        ['Optimizing Large Tables', 'Practical tips for keeping tables fast when datasets grow into the millions.', 'published', 9.3, true],
        ['Custom Widgets from Scratch', 'Design and build your own dashboard widgets with Livewire.', 'draft', 6.8, false],
        ['Authorization Patterns in Panels', 'Use policies and gates to control access to resources and pages.', 'published', 8.8, false],
        ['Testing Filament Apps with Pest', 'Write expressive tests for forms, tables, and actions using Pest.', 'published', 9.0, false],
        ['Global Search Under the Hood', 'How global search works and how to fine-tune its results.', 'reviewing', 7.2, false],
        ['Theming with CSS Hooks', 'Customize the look of your panel using semantic CSS hook classes.', 'published', 8.1, false],
        ['Import and Export Workflows', 'Let users import CSVs and export filtered data with built-in actions.', 'published', 8.5, false],
        ['Notifications that Delight', 'Craft helpful flash and database notifications for your users.', 'draft', 6.5, false],
        ['Multi-panel Architectures', 'When and how to split your application into multiple panels.', 'published', 7.8, false],
        ['Wizard Forms for Onboarding', 'Guide users through complex flows with multi-step wizards.', 'published', 8.3, false],
        ['Managing File Uploads', 'Configure disks, visibility, and image editing for upload fields.', 'reviewing', 7.5, false],
        ['Relation Managers in Practice', 'Manage related records inline on your resource pages.', 'published', 8.7, false],
        ['Custom Fields with Alpine.js', 'Build interactive custom form fields powered by Alpine.js.', 'published', 9.2, true],
        ['Scheduled Reports with Widgets', 'Combine charts and queued jobs to deliver scheduled insights.', 'draft', 6.9, false],
        ['Slug Generation Strategies', 'Generate unique, readable slugs for your content automatically.', 'published', 7.3, false],
        ['Infolists for Read-only Views', 'Present record details beautifully without building a form.', 'published', 8.0, false],
        ['Bulk Actions Done Right', 'Design safe, fast bulk operations for large selections.', 'reviewing', 7.7, false],
        ['Dark Mode Best Practices', 'Ensure your customizations look great in both light and dark themes.', 'published', 8.9, false],
        ['Reactive Forms Deep Dive', 'Understand partial rendering and field dependencies in forms.', 'published', 9.4, true],
        ['Soft Deletes and Trash Filters', 'Give users a safety net with restorable, soft-deleted records.', 'published', 7.1, false],
        ['Custom Pages Beyond CRUD', 'Build settings screens, reports, and dashboards as custom pages.', 'draft', 6.7, false],
        ['Tenant-aware File Storage', 'Scope uploads per tenant with dynamic disk configuration.', 'published', 8.4, false],
        ['Speeding up Livewire Requests', 'Profile and reduce payload sizes for snappier interactions.', 'published', 8.6, false],
        ['Data Integrity with Form Validation', 'Combine client hints and server rules for trustworthy input.', 'reviewing', 7.4, false],
        ['Charts that Tell a Story', 'Choose the right chart type and polish it for your dashboard.', 'published', 8.2, false],
        ['Reusable Form Components', 'Extract shared field groups into composable schema components.', 'published', 7.9, false],
        ['Handling Money and Currencies', 'Store, format, and total monetary values without rounding errors.', 'published', 9.0, false],
        ['Keyboard Shortcuts for Power Users', 'Add key bindings that make your admin fly.', 'draft', 6.4, false],
        ['Audit Trails with Model Events', 'Track who changed what with lightweight event listeners.', 'published', 8.1, false],
        ['Localizing your Panel', 'Translate labels, notifications, and validation messages.', 'reviewing', 7.0, false],
        ['Zero-downtime Deployments', 'Ship updates safely with health checks and graceful restarts.', 'published', 8.8, false],
        ['Designing Friendly Empty States', 'Turn blank tables into helpful calls to action.', 'published', 7.6, false],
        ['API Tokens for Integrations', 'Issue and revoke tokens for third-party integrations securely.', 'draft', 6.6, false],
        ['Query Builder Power Tips', 'Let users compose complex constraints with the query builder.', 'published', 8.5, false],
        ['Accessible Admin Interfaces', 'Practical accessibility wins for keyboard and screen-reader users.', 'published', 9.6, true],
        ['Caching Expensive Dashboards', 'Keep dashboards fresh without hammering your database.', 'reviewing', 7.8, false],
        ['From Spreadsheet to Admin Panel', 'Migrate a team off spreadsheets with importers and validation.', 'published', 8.3, false],
        ['Writing a Filament Plugin', 'Package your components and share them with the community.', 'published', 9.1, false],
    ];

    /**
     * @var array<array{0: string, 1: string}>
     */
    protected const array USERS = [
        ['Aisha Patel', 'Developer'],
        ['Marcus Webb', 'Designer'],
        ['Elena Petrova', 'Product Manager'],
        ['Tom Hardwick', 'QA Engineer'],
        ['Priya Sharma', 'Marketing Lead'],
        ['Lucas Meyer', 'Support Specialist'],
        ['Sofia Rossi', 'Developer'],
        ['James Okafor', 'Designer'],
        ['Hannah Berg', 'Product Manager'],
        ['Mateo Alvarez', 'QA Engineer'],
        ['Grace Chen', 'Marketing Lead'],
        ['Oliver Nowak', 'Support Specialist'],
        ['Amara Diallo', 'Developer'],
        ['Felix Wagner', 'Designer'],
        ['Isla MacLeod', 'Product Manager'],
        ['Noah Fischer', 'QA Engineer'],
        ['Leila Haddad', 'Marketing Lead'],
        ['Ethan Brooks', 'Support Specialist'],
        ['Mia Kowalski', 'Developer'],
        ['Ravi Menon', 'Designer'],
        ['Clara Lindberg', 'Product Manager'],
        ['Sam Whitfield', 'QA Engineer'],
        ['Yuki Tanaka', 'Marketing Lead'],
        ['Nina Novak', 'Support Specialist'],
        ['Leo Marchetti', 'Developer'],
        ['Freya Jensen', 'Designer'],
        ['Omar Farouk', 'Product Manager'],
        ['Lucy Tran', 'QA Engineer'],
        ['Hugo Moreau', 'Marketing Lead'],
        ['Ada Osei', 'Support Specialist'],
        ['Jack Delaney', 'Developer'],
        ['Rosa Fuentes', 'Designer'],
        ['Finn Gallagher', 'Product Manager'],
        ['Zara Ahmed', 'QA Engineer'],
        ['Max Keller', 'Marketing Lead'],
        ['Ines Costa', 'Support Specialist'],
        ['Theo Van Dijk', 'Developer'],
        ['Lily Zhang', 'Designer'],
        ['Arthur Bell', 'Product Manager'],
        ['Maja Nilsson', 'QA Engineer'],
        ['Diego Ramos', 'Marketing Lead'],
        ['Emil Novotny', 'Support Specialist'],
        ['Sara Lindqvist', 'Developer'],
        ['Chloe Barnett', 'Designer'],
        ['Ben Carver', 'Product Manager'],
    ];

    /**
     * @param  array<int>  $authorIds
     * @return array<array<string, mixed>>
     */
    public static function posts(array $authorIds): array
    {
        $baseDate = Carbon::parse('2025-01-28 09:00:00');

        return collect(static::POSTS)
            ->map(fn (array $post, int $index): array => [
                'title' => $post[0],
                'slug' => Str::slug($post[0]),
                'description' => $post[1],
                'status' => $post[2],
                'rating' => $post[3],
                'is_featured' => $post[4],
                'author_id' => $authorIds[$index % count($authorIds)],
                'created_at' => $baseDate->copy()->subDays($index * 2)->toDateTimeString(),
                'updated_at' => $baseDate->copy()->subDays($index * 2)->toDateTimeString(),
            ])
            ->all();
    }

    /**
     * @return array<array<string, mixed>>
     */
    public static function users(): array
    {
        $baseDate = Carbon::parse('2024-08-01 09:00:00');

        return collect(static::USERS)
            ->map(fn (array $user, int $index): array => [
                'name' => $user[0],
                'email' => Str::of($user[0])->lower()->replace(' ', '.')->append('@example.com')->toString(),
                'email_verified_at' => '2024-09-15 10:00:00',
                'password' => 'password',
                'phone' => sprintf('+1 (555) 234-%04d', 1000 + $index),
                'job' => $user[1],
                'created_at' => $baseDate->copy()->addDays($index)->toDateTimeString(),
                'updated_at' => $baseDate->copy()->addDays($index)->toDateTimeString(),
            ])
            ->all();
    }
}
