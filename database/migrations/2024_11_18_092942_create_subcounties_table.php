<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcountiesTable extends Migration
{
    public function up()
    {
        Schema::create('subcounties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('county_id')->constrained('counties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subcounties');
    }
}
