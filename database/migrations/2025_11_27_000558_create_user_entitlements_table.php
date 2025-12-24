<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEntitlementsTable extends Migration
{
    public function up()
    {
        Schema::create('user_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('entitlement_type', ['meeting','course','tryout','quiz']);
            $table->unsignedBigInteger('entitlement_id')->default(0);
            $table->enum('source', ['purchase','bonus']);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id','entitlement_type']);
            $table->unique(['user_id', 'entitlement_type', 'entitlement_id'],'user_entitlements_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_entitlements');
    }
}
