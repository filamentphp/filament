<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Dan Harrin',
            'email' => 'dan@filamentphp.com',
            'password' => Hash::make('password'),
        ]);

        $users = collect([
            ['name' => 'Ryan Chandler', 'email' => 'ryan@filamentphp.com'],
            ['name' => 'Zep Fietje', 'email' => 'zep@filamentphp.com'],
            ['name' => 'Adam Weston', 'email' => 'adam@filamentphp.com'],
            ['name' => 'Dennis Koch', 'email' => 'dennis@filamentphp.com'],
            ['name' => 'Leandro Ferreira', 'email' => 'leandro@filamentphp.com'],
        ])->map(fn (array $data) => User::create([
            ...$data,
            'password' => Hash::make('password'),
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
                'created_at' => now()->subDays(count($posts) - $index),
                'updated_at' => now()->subDays(count($posts) - $index),
            ]);
        }

        // Soft-delete two posts for demonstrating trashed filter
        Post::where('slug', 'deploying-filament-applications')->delete();
        Post::where('slug', 'plugin-development-guide')->delete();
    }
}
