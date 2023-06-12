<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Post::factory(50)->create();
        $widgets = [
            [
                'key' => 'header',
                'image' => null,
                'title' => 'Lorem Ipsum Dolor Sit Amet JS',
                'content' => 'Lorem Ipsum Dolor Sit Amet JS',
                'active' => true
            ],
            [
                'key' => 'about-us-sidebar',
                'image' => null,
                'title' => 'About us',
                'content' => "I'm back-end developer with 3+ years of experience.",
                'active' => true
            ],
            [
                'key' => 'about-page',
                'image' => null,
                'title' => 'About us page',
                'content' => fake()->realText(5000),
                'active' => true
            ]
        ];
        DB::table('text_widgets')->insert($widgets);
        User::create([
            'name' => 'Salawat Joldasbaev',
            'email' => 'jsalawatdeveloper@gmail.com',
            'password' => Hash::make('123'),
        ]);
    }
}
