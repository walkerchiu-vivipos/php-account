<?php

namespace WalkerChiu\Account\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;

class ProfileRepository extends Repository
{
    use RepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.account.profile'));
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
                                return $query->with(['addresses', 'addresses.langs']);
                            })
                          ->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['user_id']), function ($query) use ($data) {
                                            return $query->where('user_id', $data['user_id']);
                                        })
                                        ->unless(empty($data['language']), function ($query) use ($data) {
                                            return $query->where('language', $data['language']);
                                        })
                                        ->unless(empty($data['timezone']), function ($query) use ($data) {
                                            return $query->where('timezone', $data['timezone']);
                                        })
                                        ->unless(empty($data['currency_id']), function ($query) use ($data) {
                                            return $query->where('currency_id', $data['currency_id']);
                                        })
                                        ->unless(empty($data['area']), function ($query) use ($data) {
                                            return $query->where('area', $data['area']);
                                        })
                                        ->when(isset($data['gender']), function ($query) use ($data) {
                                            return $query->where('gender', $data['gender']);
                                        })
                                        ->when(isset($data['notice_login']), function ($query) use ($data) {
                                            return $query->where('notice_login', $data['notice_login']);
                                        })
                                        ->when(isset($data['note']), function ($query) use ($data) {
                                            return $query->where('note', 'LIKE', "%".$data['note']."%");
                                        })
                                        ->when(isset($data['remarks']), function ($query) use ($data) {
                                            return $query->where('remarks', 'LIKE', "%".$data['remarks']."%");
                                        });
                            })
                            ->orderBy('updated_at', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });

        $list = [];
        foreach ($records as $record) {
            $data = $record->toArray();
            array_push($list, $data);
        }

        return $list;
    }

    /**
     * @param Profile $entity
     * @param Array|String $code
     * @return Array
     */
    public function show($entity, $code)
    {
    }
}
