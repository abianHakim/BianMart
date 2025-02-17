<?php

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
        Schema::create('voucher_diskon', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher', 50)->unique();
            $table->enum('jenis', ['persentase', 'nominal']);
            $table->double('nilai');
            $table->double('min_belanja')->default(0);
            $table->date('berlaku_hingga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_diskon');
    }
};
