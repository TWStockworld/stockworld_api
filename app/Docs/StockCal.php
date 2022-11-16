<?php

namespace App\Docs;

/**
 *      @OA\Post(
 *      path="/api/stock/cal_stock",
 *      operationId="cal_stock",
 *      tags={"4. StockCal"},
 *      summary="兩隻股票計算漲跌機率",
 *      description="兩隻股票計算漲跌機率",
 *      @OA\Parameter(
 *          name="startdate",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="enddate",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="diff",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="stockA_id",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="stockB_id",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="upordown",
 *          description="1:漲 2:跌 3:都顯示",
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
 *  *      @OA\Post(
 *      path="/api/stock/cal_stock_withoutdiff",
 *      operationId="cal_stock_withoutdiff",
 *      tags={"4. StockCal"},
 *      summary="兩隻股票計算漲跌機率",
 *      description="兩隻股票計算漲跌機率",
 *      @OA\Parameter(
 *          name="startdate",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="enddate",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="stockA_id",
 *          description="",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *  *      @OA\Parameter(
 *          name="stockB_id",
 *          description="",
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

class StockCal
{
}
