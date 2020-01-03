<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Example connection ORM
 * ELoquent : https://www.laravel.com/docs/5.3/eloquent
 *
 * @author thomas
 */
class Example extends Model
{
    /**
     * Name DataBase connection in Conf : Multi DB required
     * @var string
     */
    protected $connection = 'apiBdd';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'myTable';

    /**
     * The primary key is an incrementing integer value
     *
     * @var string
     */
    protected $primaryKey = 'id';

}