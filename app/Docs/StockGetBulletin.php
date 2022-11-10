<?php

namespace App\Docs;

/**
 * 
 *  *  @OA\Get(
 *      path="/api/stock/get_bulletin",
 *      operationId="get_bulletin",
 *      tags={"2. StockGetBulletin"},
 *      summary="股票特別種類",
 *      description="股票特別種類",
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 *  )
 * 
 *  * @OA\Post(
 *      path="/api/stock/get_stock_special_kind_detail",
 *      operationId="get_stock_special_kind_detail",
 *      tags={"2. StockGetBulletin"},
 *      summary="以特別種類代號 取得股票",
 *      description="以特別種類代號 取得股票",
 *      @OA\Parameter(
 *          name="bulletin_id",
 *          description="特別種類代號",
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
 * )
 */

class StockGetBulletin
{
}
