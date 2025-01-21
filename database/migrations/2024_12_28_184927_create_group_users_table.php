<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
            $table->datetime('invitation_expires_at')->nullable();
            $table->datetime('joined_at')->nullable();
            $table->foreignIdFor(Group::class)->constrained()->onDelete('cascade');
            $table->foreignId('inviter_id')->references('id')->on('users')->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->datetime("refused_at")->nullable();
            $table->datetime("kicked_at")->nullable();
            $table->datetime("left_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
