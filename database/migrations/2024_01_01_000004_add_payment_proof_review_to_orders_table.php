<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_review_status')->default('not_submitted');
            $table->text('payment_rejection_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_proof_path',
                'payment_review_status',
                'payment_rejection_reason',
            ]);
        });
    }
};
