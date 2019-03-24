<?php

namespace App\Repository;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class MemberRepository
 * @package App\Repositories
 */
class MemberRepository extends Repository
{

    /**
     * MemberRepository constructor.
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        parent::__construct($member);
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function create(string $email, string $password,string $name)
    {
        $member = $this->getByMail($email);
        if (!empty($member->created_at)) {
            throw new \Exception('此Email已被註冊');
        }

        $member->password = Hash::make($password);
        $member->name = $name;
        $member->token = sha1(uniqid(microtime(true)));
        return $member->save();
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception
     */
    public function login(string $email, string $password)
    {
        $member = $this->getByMail($email);
        if (empty($member->created_at)) {
            throw new \Exception('帳號不存在');
        }

        if (!Hash::check($password, $member->password)) {
            throw new \Exception('密碼錯誤');
        }
        $member->makeHidden('password');
        $member->makeVisible('token');
        return $member->toArray();
    }

    /**
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getByMail(string $email)
    {
        return $this->model->firstOrNew(['email' => $email]);
    }

    public function facebookRegisterOrLogin($email, $fbId, $expires_at)
    {
        $member = Member::firstOrNew(['fb_id' => $fbId]);
        //  如果有取得facebook的email資訊
        if ($email != '') {
            //  如果email沒有且沒有和其他人重複，不管新舊會員都可以回填
            if (empty($member->email) && Member::where('email', $email)->get()->isEmpty()) {
                $member->email = $email;

                //  但如果email和其他人重複，只有沒建立過facebook帳號會員的人可以和原始的email帳號合併
            } else {
                if (empty($member->created_at)) {
                    $member = Member::where('email', $email)->first();
                    $member->fb_id = $fbId;

                }
            }
        }

        // 隨機生成token
        if (empty($member->created_at)) {
            $member->token = sha1(uniqid(microtime(true)));
        }
        $member->expired_at = $expires_at;
        $member->save();
    }
}