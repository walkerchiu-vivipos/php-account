<?php

namespace WalkerChiu\Account\Models\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class MemberService
{
    use CheckExistTrait;

    protected $repository;
    protected $repository_profile;

    public function __construct()
    {
        $this->repository         = App::make(config('wk-core.class.account.memberRepository'));
        $this->repository_profile = App::make(config('wk-core.class.account.profileRepository'));
    }

    /**
     * @param String $name
     * @param String $id
     * @return Boolean
     */
    public function checkExistUsername(String $name, $id = null)
    {
        return $this->repository->where('name', '=', $name)
                                ->when($id, function ($query, $id) {
                                    return $query->where('id', '<>', $id);
                                  })
                                ->exists();
    }

    /**
     * @param String $email
     * @param String $id
     * @return Boolean
     */
    public function checkExistEmail(String $email, $id = null)
    {
        return $this->repository->where('email', '=', $email)
                                ->when($id, function ($query, $id) {
                                    return $query->where('id', '<>', $id);
                                  })
                                ->exists();
    }

    /**
     * @param String $name
     * @param String $email
     * @param String $password
     * @return User
     */
    public function createMember(String $name, String $email, String $password)
    {
        $user = $this->repository->create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password)
        ]);
        $profile = $this->repository_profile->create([
            'user_id' => $user->id
        ]);

        return $user;
    }
}
