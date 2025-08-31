<?php

use App\Http\Filters\Api\ApiFilter;

class BoardFilter extends ApiFilter
{
    protected $allowedFields = [
        'name' => ['eq'],
        'description' => ['eq'],
        'owner_id' => ['eq'],
    ];

    protected $abbreviations = [
        'eq' => '=',
    ];

    protected $columnMap = [
        'owner_id' => 'ownerId',
    ];
}

?>