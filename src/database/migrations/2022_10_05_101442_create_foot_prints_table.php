<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFootPrintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('footprint.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/footprint.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['foot_prints'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('causer');
            $table->nullableMorphs('model');
            $table->string('module_name')->nullable()->comment('Module name');
            $table->json('old_value')->nullable()->comment('Old value');
            $table->json('new_value')->nullable()->comment('Updated new value');
            $table->string('action')->comment('Action:- Create/Update/Delete/View');
            $table->string('guard');
            $table->string('ip_address')->comment('Causer IP address');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('footprint.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/footprint.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['foot_prints']);
    }
}
