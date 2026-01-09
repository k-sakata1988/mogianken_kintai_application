<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_user_id')->constrained('users');
            $table->foreignId('approver_user_id')->nullable()->constrained('users');

            $table->json('before_data');
            $table->json('after_data');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending / approved / rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
