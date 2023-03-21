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
        Schema::create('4g_nurs', function (Blueprint $table) {
            $table->id();
            $table->text('impacted_sites');
            $table->integer('cells');
            $table->dateTime("begin");
            $table->dateTime("end");
            $table->decimal("nur", $precision = 8, $scale = 2);
            $table->string("system");
            $table->string("sub_system");
            $table->text('solution');
            $table->string("oz", 100);
            $table->enum('type', ['Involuntary', 'Voluntary']);
            $table->boolean("access");
            $table->boolean('Force_Majeure')->nullable();
            $table->string('Force_Majeure_type')->nullable();
            $table->enum("technology", ['4G'])->default("4G");
            $table->string('Dur_Hr');
            $table->integer('Dur_min');
            $table->integer("week");
            $table->string("gen_owner")->nullable();
            $table->integer("year");
            $table->string('problem_site_code', 100);
            $table->string('problem_site_name', 300);
            $table->integer('month');
            $table->integer('network_cells');
            $table->decimal('monthly_nur', $precision = 8, $scale = 2);
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
        Schema::dropIfExists('4g_nurs');
    }
};
