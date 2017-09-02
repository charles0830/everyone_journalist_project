<?php

use App\Category;
use App\Comment;
use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Category::truncate();
        Post::truncate();
        Comment::truncate();
        DB::table('category_post')->truncate();

        $userQuantity = 50;
        $categoryQuantatity = 15;
        $postQuantity = 100;
        $transactionQuantity = 1000;

        factory(User::class, $userQuantity)->create();
        factory(Category::class, $categoryQuantatity)->create();
        factory(Post::class, $postQuantity)->create()->each(
            function ($post) {
                $categories = Category::all()->random(mt_rand(1, 5))->pluck('id');
                $post->categories()->attach($categories);
            }
        );


        Post::all()->each(function($post,$key){
            factory(Comment::class,rand(1,10))->create([
                'post_id'=>$post->id
            ]);

        });

    }
}
