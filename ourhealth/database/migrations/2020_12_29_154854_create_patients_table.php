<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 60);
            $table->string('middle_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('id_card', 120);
            $table->string('nationality', 4)->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo_s3_key')->nullable();
            $table->foreignId('preferred_hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('blood_type', ['A+', 'B+', 'AB-', 'AB+', 'A-', 'B-', '0-', '0+'])->nullable();
            $table->foreignId('third_party_insurance_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('biological_father_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('biological_mother_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->timestamps();

            $table->unique(['id_card', 'nationality']);
            $table->foreign('nationality')->references('iso_code')->on('countries')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
