<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Category;
use App\Comment;
use App\Post;
use App\Product;
use App\Seller;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;


    $randomFile = $faker->randomElement(['1.jpg','2.jpg','3.jpg','4.jpg',
        '5.jpg','6.png']);
    $nowFile = Storage::disk('user_images')->get($randomFile);

    $image = Image::make($nowFile);
    $image->encode('jpg',50);


    $fileName = uniqid('img_').".jpg";
    $image->save(public_path('img/'.$fileName));
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => $verified = $faker->randomElement([User::VERIFIED_USER, User::UNVERIFIED_USER]),
        'verification_token' => $verified == User::UNVERIFIED_USER ? User::generateVerificationCode() : null,
        'admin' => $verified = $faker->randomElement([User::ADMIN_USER, User::REGULAR_USER]),
        'phone_no' => $faker->phoneNumber,
        'image_thumb'=>$fileName,
    ];
});


$factory->define(Category::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
    ];
});


$factory->define(Post::class, function (Faker\Generator $faker) {

    $randomFile = $faker->randomElement(['1.jpg','2.jpg','3.jpg','4.jpg',
        '5.jpg','6.jpg','7.jpg','8.jpg','9.jpg','10.jpg']);

    $nowFile = Storage::disk('post_images')->get($randomFile);

    $image = Image::make($nowFile);
    $image->encode('jpg',50);


    $fileName = uniqid('img_').".jpg";
    $image->save(public_path('img/'.$fileName));
    return [
        'title' => $faker->word,
        'description' => $faker->paragraph(1),
        'cover_image' =>$fileName,
        'user_id' => User::all()->random()->id,
    ];
});

$factory->define(Comment::class, function (Faker\Generator $faker) {
    static $post_id;
    return [
        'description' => $faker->paragraph(1),
        'user_id' => User::all()->random()->id,
        'post_id' =>$post_id
    ];
});




