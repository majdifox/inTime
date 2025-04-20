<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReviewedByDriverToRidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rides', function (Blueprint $table) {
            if (!Schema::hasColumn('rides', 'is_reviewed_by_driver')) {
                $table->boolean('is_reviewed_by_driver')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rides', function (Blueprint $table) {
            if (Schema::hasColumn('rides', 'is_reviewed_by_driver')) {
                $table->dropColumn('is_reviewed_by_driver');
            }
        });
    }
}