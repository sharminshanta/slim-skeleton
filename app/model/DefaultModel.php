<?php

namespace Sharminshanta\Web\Accounts\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class DefaultModel
 * @package Sharminshanta\Web\Accounts\Model
 */
class DefaultModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function getAll()
    {
        /**
         * @var $this Model|Builder
         */
        return $this->get();
    }
}