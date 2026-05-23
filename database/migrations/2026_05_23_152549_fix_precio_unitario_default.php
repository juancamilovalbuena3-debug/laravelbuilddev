<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ✅ Ejecutar con: php artisan migrate
// Agrega default(0) a precio_unitario y cantidad en la tabla compras
// para evitar el error: Field 'precio_unitario' doesn't have a default value

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('precio_unitario', 15, 2)->default(0)->change();
            $table->integer('cantidad')->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('precio_unitario', 15, 2)->nullable(false)->change();
            $table->integer('cantidad')->nullable(false)->change();
        });
    }
};