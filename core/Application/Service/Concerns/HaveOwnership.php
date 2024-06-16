<?php

namespace Core\Application\Service\Concerns;

trait HaveOwnership
{
    /**
     * Property to check if model is ownable.
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * Only retrieve resources that the user owns.
     *
     * @return this
     */
    public function onlyOwned()
    {
        if ($this->userIsUnrestricted() && $this->isOwnable()) {
            return $this->model;
        }

        if (! $this->userIsSuperAdmin() && $this->isOwnable()) {
            return $this->model->where('user_id', $this->auth()->id());
        }

        return $this->model;
    }

    /**
     * Check if the list should only contain
     * resources from the user.
     *
     * @return boolean
     */
    protected function isOwnable(): bool
    {
        return $this->ownable && $this->getConnection()
            ->getSchemaBuilder()->hasColumn($this->getTable(), 'user_id');
    }
}
