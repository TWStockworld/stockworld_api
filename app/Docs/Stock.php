<?php

namespace App\Docs;

/**
 * 
 *  *  @OA\Get(
 *      path="/api/stock/get_stock_category",
 *      operationId="get_stock_category",
 *      tags={"Stock"},
 *      summary="股票種類 【台股分類 -點上市或上櫃 若點上櫃 種類加 '櫃'字】",
 *      description="股票種類 【台股分類 -點上市或上櫃 若點上櫃 種類加 '櫃'字】",
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 * )
 * 
 *  * @OA\Post(
 *      path="/api/stock/get_stock_name",
 *      operationId="get_stock_name",
 *      tags={"Stock"},
 *      summary="股票基本資料 【台股分類 - 利用種類+上市上櫃篩選 用】",
 *      description="股票基本資料 【台股分類 - 利用種類+上市上櫃篩選 用】",
 *      @OA\Parameter(
 *          name="stock_category_id",
 *          description="種類id 全部:0 其他:種類代號",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *         @OA\Parameter(
 *          name="stock_type",
 *          description="上櫃:2 上市:3",
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
 * @OA\Post(
 *      path="/api/stock/get_stock",
 *      operationId="get_stock",
 *      tags={"Stock"},
 *      summary="單一股票歷年數據【關於股票 圖表用】",
 *      description="單一股票歷年數據",
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
 * )
 *  *  * @OA\Post(
 *      path="/api/stock/get_category_last_stock",
 *      operationId="get_category_last_stock",
 *      tags={"Stock"},
 *      summary="股票最新資料(以種類分類)【關於股票-右下方 相關類股用】",
 *      description="股票最新資料(以種類分類)【關於股票-右下方 相關類股用】",
 *      @OA\Parameter(
 *          name="stock_category_id",
 *          description="種類id",
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
 *  @OA\Post(
 *      path="/api/stock/get_stock_probability",
 *      operationId="get_stock_probability",
 *      tags={"股票漲跌"},
 *      summary="單一股票漲跌機率【關於股票用】",
 *      description="單一股票漲跌機率【關於股票用】",
 *      @OA\Parameter(
 *          name="stock_id",
 *          description="股票代號",
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
 * 
 *  * @OA\Get(
 *      path="/api/stock/get_all_stock_probability",
 *      operationId="get_all_stock_probability",
 *      tags={"股票漲跌"},
 *      summary="全部股票漲跌機率【首頁用】",
 *      description="全部股票漲跌機率【首頁用】",
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 * )

 */

class Stock
{
}
