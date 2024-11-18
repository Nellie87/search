<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSublocationsTable extends Migration
{
    public function up()
    {
        Schema::create('sublocations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sublocations');
    }
}
