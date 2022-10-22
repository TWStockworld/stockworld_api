<?php

namespace App\Repositories;

use App\Models\Bulletin;
use App\Models\StockCalculate;
use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\StockSpecialKindDetail;

use App\Jobs\CalculateStockJob;
use App\Models\StockCalculateGroup;

class GetStockRepository
{
    protected $user;

    // public function __construct(User $user, StockName $stockName, StockCategory $stockCategory,StockData $stockData)
    // {
    //     $this->user = $user;
    //     $this->stockName = $stockName;
    //     $this->stockCategory = $stockCategory;
    //     $this->stockData = $stockData;
    // }


    public function get_stock_category()
    {
        return response()->json(['success' => StockCategory::all()], 200);
    }
    public function get_stock_name($request)
    {
        $stock_category_id = $request->stock_category_id;
        $stock_type = $request->stock_type;
        if ($stock_category_id == 0) {
            $stocks = StockName::all();
        } else {
            if ($stock_type) {
                $stocks = StockCategory::find($request->stock_category_id)->StockName->where('type', $stock_type)->values();
            } else {
                $stocks = StockCategory::find($request->stock_category_id)->StockName;
            }
        }

        return response()->json(['count' => $stocks->count(), 'success' => $stocks,], 200);
    }


    public function get_stock($request)
    {
        $stock_id = $request->stock_id;
        $stocks = StockName::where('stock_id', $stock_id)->first();
        $stock_data = $stocks->StockData;
        $stock_category = $stocks->StockCategory;
        $last_data = $stock_data->last();
        return response()->json([
            'count' => $stocks->count(), 'stock_name' => $stocks['stock_name'], 'stock_category_id' => $stock_category['id'],
            'stock_category' => $stock_category['category'],
            'stock_data' => $stock_data, 'last_data' => $last_data
        ], 200);
    }
    public function get_all_stock_probability()
    {
        $stock_calculate_group_id = 1;
        $date = StockCalculateGroup::find($stock_calculate_group_id);

        $probability_up = StockCalculate::where('diff', '!=', 0)->where(['sort' => 1, 'stock_calculate_group_id' => $stock_calculate_group_id])->get()->sortByDesc('up')->values()->take(10);
        $probability_down = StockCalculate::where('diff', '!=', 0)->where(['sort' => 2, 'stock_calculate_group_id' => $stock_calculate_group_id])->get()->sortByDesc('down')->values()->take(10);


        $probability_up = $probability_up->map(function ($item, $key) {
            unset($item['id']);
            unset($item['stock_calculate_group_id']);
            unset($item['created_at']);
            unset($item['updated_at']);
            unset($item['down']);
            unset($item['sort']);
            $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
            $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
            $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
            $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
            unset($item['stockA_name_id']);
            unset($item['stockB_name_id']);
            $item['order'] = $key + 1;

            return $item;
        });
        $probability_down = $probability_down->map(function ($item, $key) {
            unset($item['id']);
            unset($item['stock_calculate_group_id']);
            unset($item['created_at']);
            unset($item['updated_at']);
            unset($item['up']);
            unset($item['sort']);
            $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
            $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
            $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
            $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
            unset($item['stockA_name_id']);
            unset($item['stockB_name_id']);
            $item['order'] = $key + 1;

            return $item;
        });
        return response()->json([
            'data_start_date' => $date->startdate, 'data_end_date' => $date->enddate,
            'probability_up' => $probability_up, 'probability_down' => $probability_down,
        ], 200);
    }
    public function get_stock_probability($request)
    {
        $stock_id = $request->stock_id;
        $show_zero_diff = $request->show_zero_diff;
        $stock_calculate_group_id = 1;
        $date = StockCalculateGroup::find($stock_calculate_group_id);


        $data = StockName::where('stock_id', $stock_id)->first();
        $relation = StockName::where('stock_id', $stock_id)->first();
        $probability_up = '';
        $probability_down = '';
        $relation_up = '';
        $relation_down = '';

        if ($data != null) {
            $data = $data->StockCalculateStockA;
            $data = $data->where('stock_calculate_group_id', $stock_calculate_group_id);
            $data = $data->map(function ($item) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['up'] = $item['up'] . '%';
                $item['down'] = $item['down'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                return $item;
            });

            $probability_up = $data->where('sort', 1)->sortByDesc('up')->values();
            $probability_up = $probability_up->map(function ($item, $key) {
                unset($item['down']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });
            $probability_down = $data->where('sort', 2)->sortByDesc('down')->values();
            $probability_down = $probability_down->map(function ($item, $key) {
                unset($item['up']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });

            if ($show_zero_diff == 0) {
                $probability_up = $probability_up->where('diff', '!=', 0)->sortByDesc('up')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
                $probability_down = $probability_down->where('diff', '!=', 0)->sortByDesc('down')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
            }
        }


        if ($relation != null) {
            $relation = $relation->StockCalculateStockB;
            $relation = $relation->where('stock_calculate_group_id', $stock_calculate_group_id);

            $relation = $relation->map(function ($item) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['up'] = $item['up'] . '%';
                $item['down'] = $item['down'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                return $item;
            });


            $relation_up = $relation->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->take(10)->values();
            $relation_up = $relation_up->map(function ($item, $key) {
                unset($item['down']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });
            $relation_down = $relation->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->take(10)->values();
            $relation_down = $relation_down->map(function ($item, $key) {
                unset($item['up']);
                unset($item['sort']);
                $item['order'] = $key + 1;

                return $item;
            });

            if ($show_zero_diff == 1) {
                $relation_up_zero = $relation->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['down']);
                        unset($item['sort']);

                        return $item;
                    });
                $relation_down_zero = $relation->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['up']);
                        unset($item['sort']);
                        return $item;
                    });
                $relation_up = collect([$relation_up_zero, $relation_up])->flatten(1)->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });;
                $relation_down = collect([$relation_down_zero, $relation_down])->flatten(1)->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });;
            }
        }
        return response()->json([
            'data_start_date' => $date->startdate, 'data_end_date' => $date->enddate,
            'probability_up' => $probability_up, 'probability_down' => $probability_down,
            'relation_up' => $relation_up, 'relation_down' => $relation_down
        ], 200);
    }


    public function get_bulletin()
    {
        $bulletins = Bulletin::all();
        return response()->json(['success' => $bulletins], 200);
    }
    public function get_stock_special_kind($request)
    {
        $bulletin_id = $request->bulletin_id;
        $stock_special_kind = Bulletin::find($bulletin_id)->StockSpecialKind;
        return response()->json(['success' => $stock_special_kind], 200);
    }
    public function get_stock_special_kind_detail($request)
    {
        $bulletin_id = $request->bulletin_id;
        $stock_special_kind_id = $request->stock_special_kind_id;
        $stock_special_kind_detail = StockSpecialKindDetail::where(['bulletin_id' => $bulletin_id, 'stock_special_kind_id' => $stock_special_kind_id])->get();

        $stocks = collect();
        $stock_special_kind_detail->map(function ($item) use ($stocks) {
            $stocks->push($item->stockname);
        });
        return response()->json(['success' => $stocks], 200);
    }
}
