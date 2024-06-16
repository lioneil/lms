<?php

namespace User\Models\Relations;

use Carbon\Carbon;
use Core\Enumerations\DetailType;
use Illuminate\Support\Collection;
use User\Models\Detail;

trait HasManyDetails
{
    /**
     * The detail keys from detail method.
     *
     * @var array
     */
    protected $detailKeys = [];

    /**
     * Get the details for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(Detail::class, 'user_id');
    }

    /**
     * Get the detail using the key.
     *
     * @param  string $key
     * @param  string $default
     * @return mixed
     */
    public function detail(string $key, string $default = null)
    {
        return $this->getFormattedDetailValue(
            $this->details->first(function ($detail) use ($key) {
                $this->pushDetailKey($detail->key);
                return strtolower($detail->{$this->getDetailKeyName()}) === strtolower($key);
            })->{$this->getDetailValueName()} ?? $default
        );
    }

    /**
     * Retrieve the collection of details not
     * in the pushed detailKeys.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRemainingDetails(): Collection
    {
        return $this->details->reject(function ($detail) {
            return in_array($detail->{$this->getDetailKeyName()}, $this->detailKeys);
        })->reject(function ($detail) {
            return in_array($detail->{$this->getDetailTypeName()}, DetailType::SENSITIVE());
        })->map(function ($detail) {
            $detail->{$this->getDetailValueName()} = $this->getFormattedDetailValue(
                $detail->{$this->getDetailValueName()}
            );
            return $detail;
        })->sortBy($this->getDetailKeyName());
    }

    /**
     * Retrieve the common collection of details
     * from the config file.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCommonDetails():? Collection
    {
        return $this->details->reject(function ($detail) {
            return in_array($detail->{$this->getDetailTypeName()}, DetailType::SENSITIVE());
        })->filter(function ($detail) {
            return in_array($detail->{$this->getDetailKeyName()}, config('modules.user.details.commons', []));
        })->sortBy($this->getDetailKeyName());
    }

    /**
     * Retrieve the uncommon collection of details.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getOtherDetails():? Collection
    {
        return $this->details->reject(function ($detail) {
            return in_array($detail->{$this->getDetailTypeName()}, DetailType::SENSITIVE());
        })->reject(function ($detail) {
            return in_array($detail->{$this->getDetailKeyName()}, config('modules.user.details.commons', []));
        });
    }

    /**
     * Retrieve the detail via `type` column.
     * Push each detail keys to the detailsKey array.
     *
     * @param  string $type
     * @return \Illuminate\Support\Collection
     */
    public function getDetailsOfType(string $type = 'detail'): Collection
    {
        return $this->details->where(
            $this->getDetailTypeName(), $type
        )->each(function ($detail) {
            $this->pushDetailKey($detail->{$this->getDetailKeyName()});
        });
    }

    /**
     * Retrieve the key column name.
     *
     * @return string
     */
    public function getDetailKeyName(): string
    {
        return $this->detailKeyName ?? 'key';
    }

    /**
     * Retrieve the key column name.
     *
     * @return string
     */
    public function getDetailTypeName(): string
    {
        return $this->detailTypeName ?? 'type';
    }

    /**
     * Retrieve the value column name.
     *
     * @return string
     */
    public function getDetailValueName(): string
    {
        return $this->detailValueName ?? 'value';
    }

    /**
     * Format the given detail.
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function getFormattedDetailValue($value)
    {
        if (strtotime($value)) {
            $value = Carbon::parse($value);
            if (($format = settings('formal:date', 'd-M Y')) == ':human:') {
                $value = $value->diffForHumans();
            } else {
                $value = $value->format($format);
            }
        }

        return $value;
    }

    /**
     * @param  string $key
     * @return void
     */
    protected function pushDetailKey(string $key): void
    {
        $this->detailKeys[] = $key;
    }
}
