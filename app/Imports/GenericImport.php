<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GenericImport implements ToModel, WithHeadingRow
{
    protected $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row)) {
            return null;
        }

        $attributes = [];
        $model = new $this->modelClass;
        $fillable = $model->getFillable();

        foreach ($row as $key => $value) {
            // Convert excel header (often snake_case or slug-case) to attribute name
            // Basic matching: if key exists in fillable
            $key = str_replace(' ', '_', $key);

            if (in_array($key, $fillable)) {
                $attributes[$key] = $value;
            }
        }

        if (empty($attributes)) {
            // Try to map loosely if strict match fails? 
            // For now, let's keep it safe. 
            return null;
        }

        return new $this->modelClass($attributes);
    }
}
