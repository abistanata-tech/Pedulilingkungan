<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_likes', function (Blueprint $table) {
            // FK on user_id uses the composite unique index; drop FK before dropping the index
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'likeable_id', 'likeable_type']);

            $table->enum('type', ['like', 'dislike'])->default('like')->after('likeable_type');

            // 1 user can have 1 like AND 1 dislike on the same entity, but not duplicates
            $table->unique(['user_id', 'likeable_id', 'likeable_type', 'type'], 'forum_likes_unique');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('forum_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('forum_likes_unique');
            $table->dropColumn('type');

            $table->unique(['user_id', 'likeable_id', 'likeable_type']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};