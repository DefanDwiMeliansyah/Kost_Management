<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('room_id');
            $table->string('full_name');
            $table->string('id_card_number', 50);
            $table->string('phone_number', 20);
            $table->string('email');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone', 20);
            $table->string('occupation', 100)->nullable();
            $table->string('institution_name')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->integer('rent_duration')->default(1);
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'moved_out'])->default('active');
            $table->string('id_card_photo')->nullable();
            $table->string('tenant_photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            // Indexes
            $table->index('status');
            $table->index('check_in_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
