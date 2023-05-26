<?php

use ZaghloulSoft\LaravelAuthorization\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateRolesTable.
 */
class CreateRolesTable extends Migration
{
    protected $model;
    protected $table;

    public function __construct()
    {
        $this->model = $this->getModel();
        $this->table = $this->model->getTable();
    }

    /**
     * @return Model
     */
    protected function getModel() : Model
    {
        return app(Role::class);
    }

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table) {
            $table->id();
            $table->string('guard');
            $table->string('name')->unique();
            $table->json('moduled_permissions');
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
		Schema::drop($this->table);
	}
}
