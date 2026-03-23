<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Link;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Models\WebsitePage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Pin timestamps so screenshots are reproducible across reseeds
        $baseDate = '2025-06-01 09:00:00';

        // Use a fixed password hash so the database is byte-identical across reseeds.
        // This is bcrypt('password') with a fixed salt — only used for doc screenshots.
        $passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        $admin = User::create([
            'name' => 'Dan Harrin',
            'email' => 'dan@filamentphp.com',
            'password' => $passwordHash,
            'created_at' => $baseDate,
            'updated_at' => $baseDate,
        ]);

        $users = collect([
            ['name' => 'Ryan Chandler', 'email' => 'ryan@filamentphp.com'],
            ['name' => 'Zep Fietje', 'email' => 'zep@filamentphp.com'],
            ['name' => 'Adam Weston', 'email' => 'adam@filamentphp.com'],
            ['name' => 'Dennis Koch', 'email' => 'dennis@filamentphp.com'],
            ['name' => 'Leandro Ferreira', 'email' => 'leandro@filamentphp.com'],
        ])->map(fn (array $data) => User::create([
            ...$data,
            'password' => $passwordHash,
            'created_at' => $baseDate,
            'updated_at' => $baseDate,
        ]));

        $allUsers = collect([$admin, ...$users]);

        $posts = [
            ['title' => 'Introducing Filament v4', 'slug' => 'introducing-filament-v4', 'status' => 'published', 'is_featured' => true, 'rating' => 9, 'description' => 'Discover what is new in Filament v4, including a refreshed design, improved performance, and powerful new features for building admin panels.', 'author_index' => 0],
            ['title' => 'Building Admin Panels with Laravel', 'slug' => 'building-admin-panels', 'status' => 'published', 'is_featured' => true, 'rating' => 8, 'description' => 'Learn how to build beautiful admin panels using Filament and Laravel, with step-by-step examples covering resources, forms, and tables.', 'author_index' => 1],
            ['title' => 'Advanced Table Techniques', 'slug' => 'advanced-table-techniques', 'status' => 'published', 'is_featured' => false, 'rating' => 7, 'description' => 'Master advanced table features including custom columns, bulk actions, and complex filters for data-heavy applications.', 'author_index' => 2],
            ['title' => 'Form Validation Best Practices', 'slug' => 'form-validation-best-practices', 'status' => 'published', 'is_featured' => false, 'rating' => 8, 'description' => 'Explore best practices for validating form inputs in Filament, including conditional validation, custom rules, and real-time feedback.', 'author_index' => 3],
            ['title' => 'Custom Themes and Styling', 'slug' => 'custom-themes-and-styling', 'status' => 'published', 'is_featured' => false, 'rating' => 6, 'description' => 'A comprehensive guide to customizing the look and feel of your Filament admin panel with custom themes, colors, and CSS overrides.', 'author_index' => 4],
            ['title' => 'Multi-tenancy in Filament', 'slug' => 'multi-tenancy-in-filament', 'status' => 'draft', 'is_featured' => false, 'rating' => 9, 'description' => 'Implement multi-tenancy in your Filament application with team-based or domain-based tenant scoping and resource isolation.', 'author_index' => 0],
            ['title' => 'Notification System Deep Dive', 'slug' => 'notification-system-deep-dive', 'status' => 'reviewing', 'is_featured' => false, 'rating' => 7, 'description' => 'Understand how to use database, broadcast, and real-time notifications to keep users informed about important events.', 'author_index' => 5],
            ['title' => 'Plugin Development Guide', 'slug' => 'plugin-development-guide', 'status' => 'draft', 'is_featured' => false, 'rating' => 8, 'description' => 'Step-by-step instructions for creating reusable Filament plugins that extend the framework with custom fields, actions, and pages.', 'author_index' => 1],
            ['title' => 'Optimizing Performance', 'slug' => 'optimizing-performance', 'status' => 'published', 'is_featured' => true, 'rating' => 9, 'description' => 'Tips and techniques for optimizing the performance of your Filament application, from eager loading to caching strategies.', 'author_index' => 2],
            ['title' => 'Deploying Filament Applications', 'slug' => 'deploying-filament-applications', 'status' => 'published', 'is_featured' => false, 'rating' => 5, 'description' => 'A practical guide to deploying Filament applications to production, covering server requirements, environment configuration, and CI/CD.', 'author_index' => 3],
        ];

        foreach ($posts as $index => $post) {
            Post::create([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'status' => $post['status'],
                'is_featured' => $post['is_featured'],
                'rating' => $post['rating'],
                'description' => $post['description'],
                'author_id' => $allUsers[$post['author_index']]->id,
                'created_at' => Carbon::parse($baseDate)->subDays(count($posts) - $index),
                'updated_at' => Carbon::parse($baseDate)->subDays(count($posts) - $index),
            ]);
        }

        // Create comments on the first few posts
        $comments = [
            ['post_index' => 0, 'user_index' => 1, 'body' => 'This is a great introduction to the new version! Looking forward to trying it out.', 'is_approved' => true],
            ['post_index' => 0, 'user_index' => 2, 'body' => 'The performance improvements alone make this worth upgrading.', 'is_approved' => true],
            ['post_index' => 0, 'user_index' => 3, 'body' => 'Will there be a migration guide for v3 users?', 'is_approved' => false],
            ['post_index' => 1, 'user_index' => 0, 'body' => 'Excellent tutorial! The step-by-step examples are really clear.', 'is_approved' => true],
            ['post_index' => 1, 'user_index' => 4, 'body' => 'Could you add a section about custom themes?', 'is_approved' => true],
            ['post_index' => 2, 'user_index' => 5, 'body' => 'The bulk actions section was exactly what I needed.', 'is_approved' => true],
            ['post_index' => 3, 'user_index' => 1, 'body' => 'How does this work with Livewire v3 validation?', 'is_approved' => false],
            ['post_index' => 4, 'user_index' => 2, 'body' => 'The CSS overrides section needs more examples.', 'is_approved' => true],
        ];

        $allPosts = Post::all();

        foreach ($comments as $index => $comment) {
            Comment::create([
                'post_id' => $allPosts[$comment['post_index']]->id,
                'user_id' => $allUsers[$comment['user_index']]->id,
                'body' => $comment['body'],
                'is_approved' => $comment['is_approved'],
                'created_at' => Carbon::parse($baseDate)->subDays(count($comments) - $index),
                'updated_at' => Carbon::parse($baseDate)->subDays(count($comments) - $index),
            ]);
        }

        // Soft-delete two posts for demonstrating trashed filter
        Post::where('slug', 'deploying-filament-applications')->delete();
        Post::where('slug', 'plugin-development-guide')->delete();

        // Create tags for simple modal resource demo
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'color' => 'danger'],
            ['name' => 'Livewire', 'slug' => 'livewire', 'color' => 'primary'],
            ['name' => 'Filament', 'slug' => 'filament', 'color' => 'warning'],
            ['name' => 'Alpine.js', 'slug' => 'alpinejs', 'color' => 'info'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'color' => 'success'],
        ];

        foreach ($tags as $tag) {
            Tag::create([...$tag, 'created_at' => $baseDate, 'updated_at' => $baseDate]);
        }

        // Attach tags to posts for relation manager demo
        $allTags = Tag::all();
        $allPosts = Post::withTrashed()->get();

        // Post 1 (Introducing Filament v4) — Laravel, Filament, Livewire
        $allPosts[0]->tags()->attach([$allTags[0]->id, $allTags[2]->id, $allTags[1]->id]);
        // Post 2 (Building Admin Panels) — Laravel, Filament
        $allPosts[1]->tags()->attach([$allTags[0]->id, $allTags[2]->id]);
        // Post 3 (Advanced Table Techniques) — Filament, Alpine.js
        $allPosts[2]->tags()->attach([$allTags[2]->id, $allTags[3]->id]);
        // Post 4 (Form Validation) — Laravel, Livewire
        $allPosts[3]->tags()->attach([$allTags[0]->id, $allTags[1]->id]);
        // Post 5 (Custom Themes) — Tailwind CSS, Filament
        $allPosts[4]->tags()->attach([$allTags[4]->id, $allTags[2]->id]);

        // Create links on first post for relation manager demo
        $links = [
            ['post_index' => 0, 'url' => 'https://filamentphp.com/docs', 'label' => 'Documentation'],
            ['post_index' => 0, 'url' => 'https://github.com/filamentphp/filament', 'label' => 'GitHub Repository'],
            ['post_index' => 1, 'url' => 'https://laravel.com/docs', 'label' => 'Laravel Docs'],
        ];

        foreach ($links as $index => $link) {
            Link::create([
                'post_id' => $allPosts[$link['post_index']]->id,
                'url' => $link['url'],
                'label' => $link['label'],
                'created_at' => Carbon::parse($baseDate)->subDays(count($links) - $index),
                'updated_at' => Carbon::parse($baseDate)->subDays(count($links) - $index),
            ]);
        }

        // Create homepage for singular resource demo
        WebsitePage::create([
            'title' => 'Welcome to Filament',
            'content' => '<p>Filament is a collection of beautiful full-stack components for Laravel. You can use it to build admin panels, customer-facing apps, Software-as-a-Service platforms, and more.</p><h2>Getting Started</h2><p>Visit the <strong>documentation</strong> to learn how to install Filament and start building your first application. Our step-by-step guides will walk you through creating resources, forms, tables, and widgets.</p>',
            'is_homepage' => true,
            'created_at' => $baseDate,
            'updated_at' => $baseDate,
        ]);

        // Enable app MFA on admin user for challenge screenshot
        $admin->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');

        // Create teams for tenancy demo
        $acme = Team::create(['name' => 'Acme Inc', 'slug' => 'acme-inc', 'created_at' => $baseDate, 'updated_at' => $baseDate]);
        $starlight = Team::create(['name' => 'Starlight Labs', 'slug' => 'starlight-labs', 'created_at' => $baseDate, 'updated_at' => $baseDate]);
        $forge = Team::create(['name' => 'Forge Digital', 'slug' => 'forge-digital', 'created_at' => $baseDate, 'updated_at' => $baseDate]);

        // Admin belongs to all teams
        $admin->teams()->attach([$acme->id, $starlight->id, $forge->id]);

        // Other users belong to one or two teams
        $users[0]->teams()->attach([$acme->id, $starlight->id]);
        $users[1]->teams()->attach([$acme->id]);
        $users[2]->teams()->attach([$starlight->id, $forge->id]);
        $users[3]->teams()->attach([$forge->id]);
        $users[4]->teams()->attach([$acme->id, $forge->id]);
    }
}
