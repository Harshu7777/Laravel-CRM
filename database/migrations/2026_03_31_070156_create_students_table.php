<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('name');
            $table->string('college');
            $table->string('department')->default('N/A');   
            $table->string('course')->default('N/A');        
            $table->string('year')->default('N/A');          
            $table->string('location')->default('N/A'); 
            $table->string('city');
            $table->string('state');
            $table->string('email')->unique();
            $table->string('phone')->default('N/A');         
            $table->decimal('gpa', 4, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};