<?php

namespace App\Docs;

/**
 * @OA\Post(
 *      path="/api/stock/get_stock",
 *      operationId="get_stock",
 *      tags={"Stock"},
 *      summary="取得股票",
 *      description="取得股票",
 *      @OA\Parameter(
 *          name="stock_id",
 *          description="股票代號",
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
 * )

 */

class Stock
{
}
