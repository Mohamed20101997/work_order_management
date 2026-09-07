<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->dropForeign(['asset_type_id']);
            $table->foreign('asset_type_id')->references('id')->on('asset_types')->cascadeOnDelete();
        });

        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->dropForeign(['work_order_id']);
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
        });

        Schema::table('part_usages', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->dropForeign(['part_id']);
            $table->foreign('part_id')->references('id')->on('parts')->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->dropForeign(['asset_type_id']);
            $table->foreign('asset_type_id')->references('id')->on('asset_types');
        });

        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->dropForeign(['work_order_id']);
            $table->foreign('work_order_id')->references('id')->on('work_orders');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')->references('id')->on('assets');
        });

        Schema::table('part_usages', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            $table->dropForeign(['part_id']);
            $table->foreign('part_id')->references('id')->on('parts');
        });
    }
};
