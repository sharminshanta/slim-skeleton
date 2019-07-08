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
    public function getEmployees()
    {
        $employees = new DefaultModel();
        return $employees;
    }
}