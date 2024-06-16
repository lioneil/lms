<?php

namespace Template\Models\Relations;

use Template\Models\Template;

trait BelongsToTemplate
{
    /**
     * Retrieve the template that this resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Retrieve the template name of the resource.
     *
     * @return string
     */
    public function getTemplateAttribute()
    {
        return $this->template->name ?? null;
    }
}
