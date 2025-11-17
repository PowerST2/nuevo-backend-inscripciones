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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->nullable()->constrained('periods')->onDelete('cascade');
            $table->string('code', 6)->nullable()->index();
            $table->string('code_cepre', 100)->nullable()->index();
            $table->string('paternal_surname', 50)->nullable()->index();
            $table->string('maternal_surname', 50)->nullable()->index();
            $table->string('names', 100)->nullable()->index();
            $table->foreignId('document_type_id')->nullable();
            $table->string('document_number', 20)->nullable()->index();
            $table->string('email', 100)->nullable()->index();
            $table->decimal('size', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('genders')->onDelete('cascade');
            $table->string('cellular_phone', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('other_phone', 30)->nullable();
            $table->foreignId('ubigeo_id')->nullable()->constrained('ubigeos')->onDelete('cascade');
            $table->string('direction', 255)->nullable();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('university_id')->nullable()->constrained('universities')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
            $table->bigInteger('start_study')->nullable();
            $table->bigInteger('end_study')->nullable();
            $table->date('date_birth')->nullable();
            $table->foreignId('country_birth_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('ubigeo_birth_id')->nullable()->constrained('ubigeos')->onDelete('cascade');
            $table->foreignId('faculties_id')->nullable()->constrained('faculties')->onDelete('cascade');
            $table->foreignId('modality1_id')->nullable()->constrained('modalities')->onDelete('cascade');
            $table->foreignId('modality2_id')->nullable()->constrained('modalities')->onDelete('cascade');
            $table->foreignId('speciality1_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->foreignId('speciality2_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->foreignId('speciality3_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->foreignId('speciality4_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->foreignId('speciality5_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->foreignId('speciality6_id')->nullable()->constrained('majors')->onDelete('cascade');
            $table->string('classroom1_id')->nullable();
            $table->string('classroom2_id')->nullable();
            $table->string('classroom3_id')->nullable();
            $table->string('classroom_voca_id')->nullable();
            $table->boolean('annulled')->default(false);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
