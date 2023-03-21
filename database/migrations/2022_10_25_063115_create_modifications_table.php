<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modifications', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('site_code');
            $table->foreign('site_code')->references('site_code')->on('sites')->cascadeOnUpdate()->cascadeOnDelete();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->string('site_name');
            $table->enum('subcontractor',['','OT','Alandick','Tri-Tech','Siatnile','Merc','GP','Systel','MBV','TELE-TECH','SAG','LM',"Red Tech","HAS","MERG"]);
            $table->enum('requester',['','Acquisition','Civil Team','Maintenance','Radio','rollout','Transmission','GA','Soc','Sharing team']);
            $table->text('action');
            $table->enum('status',['in progress','done','waiting D6'])->default("in progress");
            $table->enum('project',['Normal Modification','NTRA','Repair','LDN','LTE','Retrofitting','Sharing','Critical repair','Adding sec','L2600'])->default('Normal Modification');
            $table->date('request_date');
            $table->date('finish_date')->nullable();
            $table->string('materials')->nullable();
            $table->decimal('cost', $precision = 8, $scale = 2)->nullable();
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
        Schema::dropIfExists('modifications');
    }
};
