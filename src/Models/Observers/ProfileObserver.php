<?php

namespace WalkerChiu\Account\Models\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use WalkerChiu\Currency\Models\Services\CurrencyService;

class ProfileObserver
{
    /**
     * Handle the entity "retrieved" event.
     *
     * @param  $entity
     * @return void
     */
    public function retrieved($entity)
    {
        //
    }

    /**
     * Handle the entity "creating" event.
     *
     * @param  $entity
     * @return void
     */
    public function creating($entity)
    {
    }

    /**
     * Handle the entity "created" event.
     *
     * @param  $entity
     * @return void
     */
    public function created($entity)
    {
        //
    }

    /**
     * Handle the entity "updating" event.
     *
     * @param  $entity
     * @return void
     */
    public function updating($entity)
    {
        //
    }

    /**
     * Handle the entity "updated" event.
     *
     * @param  $entity
     * @return void
     */
    public function updated($entity)
    {
        if (Auth::check() && Auth::id() == $entity->user_id) {
            if (!is_null($entity->timezone)) {
                Session::put('timezone', $entity->timezone);
            }
        }
    }

    /**
     * Handle the entity "saving" event.
     *
     * @param  $entity
     * @return void
     */
    public function saving($entity)
    {
        if (!is_null($entity->language)) {
            if (!in_array($entity->language, config('wk-core.class.core.language')::getCodes()))
                return false;
        }
        if (!is_null($entity->timezone)) {
            if (!in_array($entity->timezone, config('wk-core.class.core.timeZone')::getValues()))
                return false;
        }
        if (config('wk-account.onoff.currency')) {
            $service = new CurrencyService();
            if (!is_null($entity->currency_id)) {
                if (in_array($entity->currency_id, $service->getEnabledSettingId())) {
                    Session::put('timezone', $entity->currency_id);
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Handle the entity "saved" event.
     *
     * @param  $entity
     * @return void
     */
    public function saved($entity)
    {
        //
    }

    /**
     * Handle the entity "deleting" event.
     *
     * @param  $entity
     * @return void
     */
    public function deleting($entity)
    {
        //
    }

    /**
     * Handle the entity "deleted" event.
     *
     * Its Lang will be automatically removed by database.
     *
     * @param  $entity
     * @return void
     */
    public function deleted($entity)
    {
        //
    }

    /**
     * Handle the entity "restoring" event.
     *
     * @param  $entity
     * @return void
     */
    public function restoring($entity)
    {
        //
    }

    /**
     * Handle the entity "restored" event.
     *
     * @param  $entity
     * @return void
     */
    public function restored($entity)
    {
        //
    }
}
