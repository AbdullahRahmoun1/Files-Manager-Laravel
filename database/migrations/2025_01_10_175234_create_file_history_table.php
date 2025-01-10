<?php

use App\Models\CheckIn;
use App\Models\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_histories', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('comparison_path')->nullable();
            $table->unsignedFloat('version');
            $table->foreignIdFor(CheckIn::class)->nullable()->constrained();
            $table->foreignIdFor(File::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('file_histories');
    }
};
