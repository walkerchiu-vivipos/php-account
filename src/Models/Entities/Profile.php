<?php

namespace WalkerChiu\Account\Models\Entities;

use WalkerChiu\Core\Models\Entities\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'language', 'timezone', 'currency_id',
        'gender', 'notice_login',
        'note', 'remarks',
        'addresses', 'images'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'notice_login' => 'boolean',
        'addresses'    => 'json',
        'images'       => 'json'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.account.profiles');

        parent::__construct($attributes);
    }

    public function languageText()
    {
        return config('wk-core.class.core.language')::all()[$this->language];
    }

    public function genderText()
    {
        switch ($this->gender) {
            case "woman":
                return trans('php-account::system.profile.gender.options.woman');
            break;

            case "man":
                return trans('php-account::system.profile.gender.options.man');
            break;

            default:
                return '-';
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('wk-core.class.user'), 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(config('wk-core.class.currency.currency'), 'currency_id', 'id');
    }

    /**
     * @param $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses($type = null) {
        return $this->morphMany(config('wk-core.class.morph-address.address'), 'morph')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            });
    }

    /**
     * @param $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function webs($type = null) {
        return $this->morphMany(config('wk-core.class.morph-web.web'), 'morph')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments() {
        return $this->morphMany(config('wk-core.class.payment.payment'), 'morph');
    }
}
