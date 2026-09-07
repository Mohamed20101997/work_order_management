<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->string('asset_number', 30)->nullable()->unique();
            $table->foreignId('asset_type_id')->constrained();
            $table->string('name');
            $table->string('serial_number')->nullable()->index();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('received_date')->index();
            $table->date('expected_release_date')->nullable();
            $table->date('released_date')->nullable();
            $table->string('status', 30)->default('received')->index();
            $table->string('priority', 10)->default('normal');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
