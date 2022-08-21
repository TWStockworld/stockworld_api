<?php

namespace App\Docs;

/**
 * @OA\Post(
 *      path="/api/auth/register",
 *      operationId="register",
 *      tags={"Auth"},
 *      summary="註冊",
 *      description="註冊",
 *      @OA\Parameter(
 *          name="name",
 *          description="姓名",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="account",
 *          description="帳號",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="password",
 *          description="密碼",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="email",
 *          description="Email",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       ),
 *      @OA\Response(
 *          response=401,
 *          description="填寫錯誤"
 *       ),
 *      @OA\Response(
 *          response=402,
 *          description="資料庫錯誤"
 *       )
 * )
 * 註冊


 * @OA\Post(
 *      path="/api/auth/login",
 *      operationId="login",
 *      tags={"Auth"},
 *      summary="登入",
 *      description="登入",
 *      @OA\Parameter(
 *          name="account",
 *          description="帳號",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="password",
 *          description="密碼",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 *      ,@OA\Response(
 *          response=401,
 *          description="登入失敗"
 *       ),
 * )
 * 登入

 * @OA\Post(
 *      path="/api/logout",
 *      operationId="Logout",
 *      tags={"Auth"},
 *      summary="登出",
 *      description="登出",
 *      security={
 *         {
 *              "Authorization": {}
 *         }
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 * )
 * 登出


 * @OA\Get(
 *      path="/api/user",
 *      operationId="getdata",
 *      tags={"Auth"},
 *      summary="取得資料",
 *      description="取得資料",
 *      security={
 *         {
 *              "Authorization": {}
 *         }
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 * )
 * 取得資料
 */

class Route
{
}
