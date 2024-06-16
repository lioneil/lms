<?php

namespace User\Services;

use Core\Application\Service\Service;
use Core\Enumerations\DetailType;
use Illuminate\Http\Request;
use User\Enumerations\CredentialColumns;
use User\Models\Account;
use User\Models\Detail;
use User\Models\User;

class DetailService extends Service implements DetailServiceInterface
{
    /**
     * The property on class instances.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \User\Models\Detail      $detail
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Detail $detail, Request $request)
    {
        $this->model = $detail;

        $this->request = $request;
    }

    /**
     * Save the user credentials to the details table
     * if the passed user does not belong to the
     * superadmin group.
     *
     * @param  \User\Models\User $user
     * @param  array             $attributes
     * @return mixed
     */
    public function record(User $user, array $attributes)
    {
        if ($user->isSuperAdmin()) {
            return;
        }

        foreach (CredentialColumns::all() as $key) {
            $user->details()->updateOrCreate([
                $user->getDetailKeyName() => $key
            ], [
                $user->getDetailValueName() => $attributes[$key] ?? null,
                $user->getDetailTypeName() => DetailType::ACCOUNT,
            ]);
        }

        return $user;
    }
}
