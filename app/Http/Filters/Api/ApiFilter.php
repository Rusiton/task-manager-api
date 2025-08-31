<?php

namespace App\Http\Filters\Api;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $allowedFields = []; // Every direct child must specify these as <string> $db_field => <array> $allowed_comparators.
    /**
     * Example: 'birthDate' => ['eq', 'lt', 'gt'], means that in the DataBase,
     * the specified model can be filtered by its birthDate field using =, < or > comparators.
     */

    protected $abbreviations = []; // Every direct child must specify these as <string> $comparator => <string> $db_comparator.
    /**
     * Example: 'eq' => '=', means that 'eq' will be transformed to '=' before being sent into the DB query.
     * These abbreviations should be the same that are used in the allowedFields array, otherwise, an error can occur
     * or the fields will be ignored, even if the user had requested them.
     */

    protected $columnMap = []; // Every direct child must specify these as <string> $lower_field => <string> $camelField.
    /**
     * Example: 'birth_date' => 'birthDate' means that 'birthDate' will be transformed to 'birth_date' before being sent into the
     * DB query.
     * These should only be specified if the http query string will accept any camelCase parameter.
     * If there is any camelCase parameter that hasn't been transformed to lower_case, the DB engine will try to find a column named
     * in camelCase (All DB columns are named using lower_case), which will produce an error.
     */



    public function transform(Request $request) {
        $DBquery = []; // Array of arrays that will contain all the DB queries.

        foreach ($this->allowedFields as $field => $operators) { // Parses through the allowd fields of the model.
            if (!array_key_exists($field, $request->all())) { continue; }
            // If the current field is not in the request query, it hops to the next iteration.
            
            $currentRequestField = $request->all()[$field]; // Gets the current field to filter out.
            foreach ($operators as $operator) { // Parses through the allowed operators for the current field.
                if (!array_key_exists($operator, $currentRequestField)) { continue; }
                // If the current operator is not included in the request query, it hops to the next iteration.

                $DBfield = $field;

                if(array_key_exists($field, $this->columnMap)) {
                    $DBfield = $this->columnMap[$field];
                    // If the current field is within the columnMap array, it transforms it from camelCase to lower_case.
                }

                $DBquery[] = [$DBfield, $this->abbreviations[$operator], $currentRequestField[$operator]];
                // Builds a new DB query as an array and stores it into the DBquery array.
            }
        }

        return $DBquery;
    }

}

?>