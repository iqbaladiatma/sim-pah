<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GenericImport implements ToModel, WithHeadingRow
{
    protected $modelClass;
    protected $mapping;

    public function __construct(string $modelClass, array $mapping = [])
    {
        $this->modelClass = $modelClass;
        $this->mapping = $mapping;
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

        // 1. Use Mapping if available
        if (!empty($this->mapping)) {
            foreach ($this->mapping as $dbField => $header) {
                if (empty($header))
                    continue;

                $slug = Str::slug($header, '_');
                if (isset($row[$slug])) {
                    $attributes[$dbField] = $row[$slug];
                }
            }
        } else {
            // 2. Fallback: Auto-match keys to fillable attributes
            foreach ($row as $key => $value) {
                // Convert excel header (often snake_case or slug-case) to attribute name
                $key = str_replace(' ', '_', $key);
                if (in_array($key, $fillable)) {
                    $attributes[$key] = $value;
                }
            }
        }

        if (empty($attributes)) {
            return null;
        }

        return new $this->modelClass($attributes);
    }
}
