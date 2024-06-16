<?php

namespace Subscription\Services;

use Illuminate\Database\Eloquent\Model;

trait HaveProgressibles
{
    /**
     * Update or create the model's progress.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  array                               $attributes
     * @return \Subscription\Models\Progression
     */
    public function progress(Model $model, array $attributes)
    {
        $progress = $model->progressions()->firstOrNew(['user_id' => $attributes['user_id']]);
        $progress->metadata = $attributes['metadata'];
        $progress->save();

        return $progress;
    }
}
