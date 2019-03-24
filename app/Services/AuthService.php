<?php

namespace App\Services;

use App\Repository\MemberRepository;
use Cookie;


/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var FacebookService
     */
    private $facebookService;

    /**
     * AuthService constructor.
     * @param MemberRepository $memberRepository
     */
    public function __construct(MemberRepository $memberRepository, FacebookService $facebookService)
    {
        $this->memberRepository = $memberRepository;
        $this->facebookService = $facebookService;
    }

    /**
     * @param $email
     * @param $password
     * @return string
     * @throws \Exception
     */
    public function login($email, $password)
    {
        $memberData = $this->memberRepository->login($email, $password);

        if (empty($memberData)) {
            throw new \Exception('登入失敗');
        }

        // 測試階段,token綁三天
        Cookie::queue('oauth_token', $memberData['token'], 60*24*3, null, null, false, true);
        return $memberData['name'];
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $name
     * @throws \Exception
     */
    public function register($email, $password, $name)
    {
        try {
            $this->memberRepository->beginTransaction();
            $result = $this->memberRepository->create($email, $password, $name);

            if (!$result) {
                throw new \Exception('註冊失敗');
            }

            $this->memberRepository->commit();
        } catch (\Exception $e) {
            $this->memberRepository->rollBack();
            throw $e;
        }
    }

    /**
     * @param $redirectUrl
     * @param $code
     * @throws \Exception
     */
    public function fbLogin($redirectUrl, $code)
    {
        try {
            $this->memberRepository->beginTransaction();

            // need to get access token
            $token_data = $this->facebookService->getAccessToken($redirectUrl, $code);

            $token_inspected = $this->facebookService->verifyAccessToken($token_data->access_token);

            if (!isset($token_inspected->data)) {
                throw new \Exception('token data error.');
            }

            $facebookMemberInfo = $this->facebookService->getFieldByToken('email,name', $token_data->access_token);
            $email = $facebookMemberInfo->email ?? '';
            $this->memberRepository->facebookRegisterOrLogin($email, $token_inspected->data->user_id, $token_inspected->data->expires_at);
            $this->memberRepository->commit();
        } catch (\Exception $e) {
            $this->memberRepository->rollBack();
            throw $e;
        }
    }

    /**
     * @param $accessToken
     * @throws \Exception
     */
    public function fbLoginByAccessToken($accessToken)
    {
        try {
            $this->memberRepository->beginTransaction();

            $token_inspected = $this->facebookService->verifyAccessToken($accessToken);

            if (!isset($token_inspected->data)) {
                throw new \Exception('token data error.');
            }

            $facebookMemberInfo = $this->facebookService->getFieldByToken('email,name', $accessToken);
            $email = $facebookMemberInfo->email ?? '';
            $this->memberRepository->facebookRegisterOrLogin($email, $token_inspected->data->user_id, $token_inspected->data->expires_at);
            $this->memberRepository->commit();
        } catch (\Exception $e) {
            $this->memberRepository->rollBack();
            throw $e;
        }
    }



}