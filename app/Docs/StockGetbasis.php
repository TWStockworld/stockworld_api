<?php

namespace App\Docs;

/**
 * 
 *  *  @OA\Get(
 *      path="/api/stock/get_stock_category",
 *      operationId="get_stock_category",
 *      tags={"1. StockBasis"},
 *      summary="股票種類",
 *      description="股票種類",
 *      @OA\Response(
 *          response=200,
 *          description="請求成功"
 *       )
 *  )
 * 
 *  * @OA\Post(
 *      path="/api/stock/get_stock_name",
 *      operationId="get_stock_name",
 *      tags={"1. StockBasis"},
 *      summary="股票基本資料",
 *      description="股票基本資料",
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
 *          description="上櫃:2 上市:3 全部:不填",
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
 *      tags={"1. StockBasis"},
 *      summary="單一股票歷年數據",
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
 *      tags={"1. StockBasis"},
 *      summary="股票最新資料(以種類分類)",
 *      description="股票最新資料(以種類分類)",
 *      @OA\Parameter(
 *          name="stock_category_id",
 *          description="種類id",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *    @OA\Parameter(
 *          name="page",
 *          description="分頁",
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

class StockGetbasis
{
}
