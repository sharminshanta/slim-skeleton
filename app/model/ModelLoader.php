<?php

namespace Sharminshanta\Web\Accounts\Model;


use Illuminate\Database\Query\Builder;

/**
 * Class ModelLoader
 * @package Sharminshanta\Web\Accounts\Model
 */
class ModelLoader
{
    /**
     * @return DefaultModel|Builder
     */
    public function getDefault()
    {
        $users = new DefaultModel();
        return $users;
    }
}