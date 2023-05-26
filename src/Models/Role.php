<?php
/** @noinspection SpellCheckingInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace ZaghloulSoft\LaravelAuthorization\Models;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'type',
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
}
