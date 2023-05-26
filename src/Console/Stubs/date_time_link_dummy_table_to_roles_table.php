<?php

use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use Illuminate\Database\Migrations\Migration;

class DummyClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::linkRelation("dummyTable","set null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::unLinkRelation("dummyTable");
    }
}
