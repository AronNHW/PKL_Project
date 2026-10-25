<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin_pusat', 'admin_cabang'])->default('admin_cabang')->after('email');
            $table->foreignId('id_lokasi')->nullable()->after('role')->constrained('lokasi')->nullOnDelete();
            $table->string('telepon', 30)->nullable()->after('id_lokasi');
            $table->boolean('is_aktif')->default(true)->after('telepon');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_lokasi']);
            $table->dropColumn(['role', 'id_lokasi', 'telepon', 'is_aktif']);
        });
    }
};
