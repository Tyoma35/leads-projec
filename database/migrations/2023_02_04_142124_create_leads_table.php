<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->integer('account_id');
            $table->string('responsible_user_name');
            $table->integer('group_id');
            $table->integer('pipeline_id');
            $table->string('pipeline_name');
            $table->integer('status_id');
            $table->string('status_name');
            $table->integer('loss_reason_id');
            $table->string('loss_reason_name');
            $table->integer('source_id');
            $table->string('source_name');
            $table->string('source_link');
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('created_at');
            $table->string('updated_at');
            $table->string('closed_at');
            $table->string('closest_task_at');
            $table->string('is_deleted');
            $table->string('custom_fields_values');
            $table->string('score');
            $table->string('tags_name');
            $table->integer('company_id');
            $table->string('company_name');
            $table->integer('company_responsible_user_id');
            $table->integer('company_group_id');
            $table->string('company_created_by');
            $table->string('company_updated_by');
            $table->string('company_created_at');
            $table->string('company_updated_at');
            $table->string('company_closest_task_at');
            $table->string('company_custom_fields_values');
            $table->string('company_account_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
