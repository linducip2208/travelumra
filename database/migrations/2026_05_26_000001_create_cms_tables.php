<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('is_published')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('photo')->nullable();
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
    }
};
