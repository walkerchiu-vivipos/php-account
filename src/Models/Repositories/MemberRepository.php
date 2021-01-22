<?php

namespace WalkerChiu\Account\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;

class MemberRepository extends Repository
{
    use RepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.user'));
    }

    /**
     * @param Array $data
     * @param Int   $page
     * @param Int   $nums per page
     * @return Array
     */
    public function list(Array $data, $page = null, $nums = null)
    {
        $this->assertForPagination($page, $nums);

        $entity = $this->entity;
        $data = array_map('trim', $data);
        $records = $entity->when( config('wk-account.onoff.morph-address') && !empty(config('wk-core.class.morph-address.address')), function ($query) {
                                return $query->with(['profile', 'profile.addresses', 'profile.addresses.langs']);
                            }, function ($query) {
                                return $query->with('profile');
                            })
                          ->when( config('wk-account.onoff.role') && !empty(config('wk-core.class.role.role')), function ($query) {
                                return $query->with('roles');
                            })
                          ->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['name']), function ($query) use ($data) {
                                            return $query->where('name', $data['name']);
                                        })
                                        ->unless(empty($data['email']), function ($query) use ($data) {
                                            return $query->where('email', $data['email']);
                                        })
                                        ->unless(empty($data['provider']), function ($query) use ($data) {
                                            return $query->where('provider', $data['provider']);
                                        })
                                        ->unless(empty($data['provider_id']), function ($query) use ($data) {
                                            return $query->where('provider_id', $data['provider_id']);
                                        })
                                        ->when(isset($data['login_at']), function ($query) use ($data) {
                                            return $query->when(is_null($data['login_at']), function ($query) {
                                                return $query->whereNull('login_at');
                                            }, function ($query) use ($data) {
                                                return $query->where('login_at', 'LIKE', "%".$data['login_at']."%");
                                            });
                                        })
                                        ->when(isset($data['is_enabled']), function ($query) use ($data) {
                                            return $query->where('is_enabled', $data['is_enabled']);
                                        })
                                        ->unless(empty($data['language']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('language', $data['language']);
                                            });
                                        })
                                        ->unless(empty($data['timezone']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('timezone', $data['timezone']);
                                            });
                                        })
                                        ->unless(empty($data['currency_id']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('currency_id', $data['currency_id']);
                                            });
                                        })
                                        ->when(isset($data['gender']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('gender', $data['gender']);
                                            });
                                        })
                                        ->when(isset($data['notice_login']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('notice_login', $data['notice_login']);
                                            });
                                        })
                                        ->when(isset($data['note']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('note', 'LIKE', "%".$data['note']."%");
                                            });
                                        })
                                        ->when(isset($data['remarks']), function ($query) use ($data) {
                                            return $query->whereHas('profile', function($query) use ($data) {
                                                $query->where('remarks', 'LIKE', "%".$data['remarks']."%");
                                            });
                                        });
                            })
                            ->orderBy('id', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });

        return $records;
    }

    /**
     * @param User $entity
     * @param Array|String $code
     * @return Array
     */
    public function show($entity, $code)
    {
    }
}
