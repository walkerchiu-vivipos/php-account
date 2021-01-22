<?php

namespace WalkerChiu\Account\Models\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use WalkerChiu\Core\Models\Entities\DateTrait;
use WalkerChiu\Core\Models\Entities\UuidTrait;

trait UserTrait
{
    use DateTrait;
    use SoftDeletes;
    use UuidTrait;

    /**
     * @param $attr
     * @return Boolean
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDisabled($query)
    {
        return $query->where('is_enabled', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(config('wk-core.class.account.profile'),
                             'user_id',
                             'id');
    }
}
