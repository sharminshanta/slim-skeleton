<?php

namespace Sharminshanta\Web\Accounts\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class DefaultModel
 * @package Sharminshanta\Web\Accounts\Model
 */
class DefaultModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'phppos_employees';

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        /**
         * @var $this Model|Builder
         */
        return $this->first();
    }
}