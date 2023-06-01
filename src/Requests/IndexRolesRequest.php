<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace ZaghloulSoft\LaravelAuthorization\Requests;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;


class IndexRolesRequest extends MainRequestStub
{
    protected ?Collection $fetchMethods;
    public ?Collection $columns;
    public ?Collection $dates;
    public ?array $selectedFetchMethod;

    protected function prepareForValidation()
    {
        $this->mergeIfMissing([
            'fetchMethod'=> 'paginate',
            'paginationCount'=> Role::getPaginationCount(),
        ]);

        $this->fetchMethods = collect([
            ['name'=> 'get','args'=> []],
            ['name'=> 'cursor','args'=> []],
            ['name'=> 'paginate','args'=> [$this->input('paginationCount'),'*']]
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fetchMethod'=> [
                Rule::in($this->fetchMethods->pluck('name'))
            ],
            'paginationCount'=> [
                'integer',
                'min:1',
                'max:1000'
            ],
            'id'=> 'integer',
            'guard'=> Rule::in(Role::getGuards()),
            'name'=> 'string',
            'created_at'=> 'date',
            'updated_at'=> 'date',
        ];
    }

    protected function passedValidation()
    {
        $this->selectedFetchMethod = $this->fetchMethods->firstWhere('name','=',$this->input('fetchMethod'));
        $this->columns = collect($this->validated())->only('id','name','guard');
        $this->dates = collect($this->validated())->only('created_at','updated_at');
    }

}
