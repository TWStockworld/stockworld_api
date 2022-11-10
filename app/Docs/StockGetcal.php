<?php

namespace App\Docs;

/**
 *    @OA\Get(
 *      path="/api/stock/get_stock_calculate_groups",
 *      operationId="get_stock_calculate_groups",
 *      tags={"3. StockGetcal"},
 *      summary="取得計算時間週期",
 *      description="取得計算時間週期",
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 *  ),
 * 
 *  @OA\Post(
 *      path="/api/stock/get_stock_probability",
 *      operationId="get_stock_probability",
 *      tags={"3. StockGetcal"},
 *      summary="單一股票漲跌機率",
 *      description="單一股票漲跌機率 時間週期代號+(以股票代號 or 種類代號 or 特別種類代號)取得資料",
 *      @OA\Parameter(
 *          name="stock_calculate_groups_id",
 *          description="時間週期代號",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="stock_id",
 *          description="股票代號",
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="stock_category_id",
 *          description="種類代號",
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="bulletin_id",
 *          description="特別種類代號",
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="show_zero_diff",
 *          description="是否要顯示相差0天的股票 0:不顯示 1:顯示",
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
 * ),
 * 
 *      @OA\Post(
 *      path="/api/stock/get_all_stock_probability",
 *      operationId="get_all_stock_probability",
 *      tags={"3. StockGetcal"},
 *      summary="全部股票漲跌機率",
 *      description="全部股票漲跌機率 時間週期代號 取得資料",
 *      @OA\Parameter(
 *          name="stock_calculate_groups_id",
 *          description="時間週期代號",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="show_zero_diff",
 *          description="是否要顯示相差0天的股票 0:不顯示 1:顯示",
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

class StockGetcal
{
}
