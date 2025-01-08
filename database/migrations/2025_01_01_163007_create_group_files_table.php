<?php

use App\Enums\GroupFileStatusEnum;
use App\Models\File;
use App\Models\Group;
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
        Schema::create('group_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(File::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Group::class)->constrained()->cascadeOnDelete();
            $table->enum('status',GroupFileStatusEnum::values())->default(GroupFileStatusEnum::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_files');
    }
};
