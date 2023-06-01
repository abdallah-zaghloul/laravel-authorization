<?php
/** @noinspection SpellCheckingInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace ZaghloulSoft\LaravelAuthorization\Models;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Role extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $casts = [
        'moduled_permissions'=> 'collection',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'guard',
        'name',
        'moduled_permissions',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $query
     * @param Collection $columns
     * @param Collection $dates
     * @return mixed
     */
    public function scopeSearch($query, Collection $columns, Collection $dates)
    {
        $columns->whenNotEmpty(fn(Collection $columns) => $columns->each(function($value,$column) use($query) {$query->where($column,"like","%$value%");}));
        $dates->whenNotEmpty(fn(Collection $dates) => $dates->each(function($value,$column) use($query) {$query->whereDate($column, '=', $value);}));
        return $query;
    }
}
