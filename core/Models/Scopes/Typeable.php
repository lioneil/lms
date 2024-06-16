<?php

namespace Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait Typeable
{
    /**
     * The type column key.
     *
     * @var string
     */
    protected $typeKey = 'type';

    /**
     * Gets all categories via given category type.
     *
     * @param  Illuminate\Database\Eloquent\Builder $builder
     * @param  string                               $type
     * @param  string                               $column
     * @return Illuminate\Database\Eloquent\Model
     */
    public function scopeType(Builder $builder, $type, $column = null)
    {
        return $builder->where($column ?? $this->typeKey, $type);
    }

    /**
     * Retrieve the type of the property.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->attributes['type'] ?? $this->type ?? $this->getTable();
    }

    /**
     * Add a global type scope to model.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string                                $type
     */
    public static function addGlobalTypeScope(Builder $builder, $type)
    {
        $builder->where((new static)->typeKey, '=', $type);
    }
}
