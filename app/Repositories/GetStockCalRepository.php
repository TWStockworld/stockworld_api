<?php

namespace App\Repositories;

use App\Models\StockCalculate;
use App\Models\StockName;
use App\Models\StockCalculateGroup;

class GetStockCalRepository
{

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
        $stock_category_id = $request->stock_category_id;

        $stock_calculate_group_id = 1;
        $date = StockCalculateGroup::find($stock_calculate_group_id);

        if ($stock_category_id) {
            $all = collect();
            $data = StockName::where('stock_category_id', $stock_category_id)->get();

            $data = $data->map(function ($item) use ($all) {
                $all->push($item->StockCalculateStockA);
                $all->push($item->StockCalculateStockB);
            });



            $relation = StockName::where('stock_category_id', $stock_category_id)->get();
        }

        $data = StockName::where('stock_id', $stock_id)->first();
        $relation = StockName::where('stock_id', $stock_id)->first();
        $probability_up = '';
        $probability_down = '';
        $relation_up = '';
        $relation_down = '';

        if ($data != null) {
            $data = $data->StockCalculateStockA;
            $data = $data->where('stock_calculate_group_id', $stock_calculate_group_id);
            $probability_up = $data->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->values()->take(10);
            $probability_up = $probability_up->map(function ($item, $key) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['up'] = $item['up'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                unset($item['down']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });

            $probability_down = $data->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->values()->take(10);
            $probability_down = $probability_down->map(function ($item, $key) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['down'] = $item['down'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                unset($item['up']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });


            if ($show_zero_diff == 1) {
                $probability_up_zero = $data->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['id']);
                        unset($item['stock_calculate_group_id']);
                        unset($item['created_at']);
                        unset($item['updated_at']);
                        $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                        $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                        $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                        $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                        $item['up'] = $item['up'] . '%';
                        unset($item['stockA_name_id']);
                        unset($item['stockB_name_id']);
                        unset($item['down']);
                        unset($item['sort']);

                        return $item;
                    });
                $probability_down_zero = $data->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['id']);
                        unset($item['stock_calculate_group_id']);
                        unset($item['created_at']);
                        unset($item['updated_at']);
                        $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                        $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                        $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                        $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                        $item['down'] = $item['down'] . '%';
                        unset($item['stockA_name_id']);
                        unset($item['stockB_name_id']);
                        unset($item['up']);
                        unset($item['sort']);
                        return $item;
                    });
                $probability_up = collect([$probability_up_zero, $probability_up])->flatten(1)->sortByDesc('up')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });;
                $probability_down = collect([$probability_down_zero, $probability_down])->flatten(1)->sortByDesc('down')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
            }
        }


        if ($relation != null) {
            $relation = $relation->StockCalculateStockB;
            $relation = $relation->where('stock_calculate_group_id', $stock_calculate_group_id);

            $relation_up = $relation->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->take(10)->values();

            $relation_up = $relation_up->map(function ($item, $key) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['up'] = $item['up'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                unset($item['down']);
                unset($item['sort']);
                $item['order'] = $key + 1;
                return $item;
            });

            $relation_down = $relation->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->take(10)->values();
            $relation_down = $relation_down->map(function ($item, $key) {
                unset($item['id']);
                unset($item['stock_calculate_group_id']);
                unset($item['created_at']);
                unset($item['updated_at']);
                $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                $item['down'] = $item['down'] . '%';
                unset($item['stockA_name_id']);
                unset($item['stockB_name_id']);
                unset($item['up']);
                unset($item['sort']);
                $item['order'] = $key + 1;

                return $item;
            });

            if ($show_zero_diff == 1) {
                $relation_up_zero = $relation->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['id']);
                        unset($item['stock_calculate_group_id']);
                        unset($item['created_at']);
                        unset($item['updated_at']);
                        $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                        $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                        $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                        $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                        $item['up'] = $item['up'] . '%';
                        unset($item['stockA_name_id']);
                        unset($item['stockB_name_id']);
                        unset($item['down']);
                        unset($item['sort']);

                        return $item;
                    });
                $relation_down_zero = $relation->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values()
                    ->map(function ($item) {
                        unset($item['id']);
                        unset($item['stock_calculate_group_id']);
                        unset($item['created_at']);
                        unset($item['updated_at']);
                        $item['stockA_name'] = StockName::get_stock_name_useid($item['stockA_name_id']);
                        $item['stockA_id'] = StockName::get_stock_id($item['stockA_name_id']);
                        $item['stockB_name'] = StockName::get_stock_name_useid($item['stockB_name_id']);
                        $item['stockB_id'] = StockName::get_stock_id($item['stockB_name_id']);
                        $item['down'] = $item['down'] . '%';
                        unset($item['stockA_name_id']);
                        unset($item['stockB_name_id']);
                        unset($item['up']);
                        unset($item['sort']);
                        return $item;
                    });
                $relation_up = collect([$relation_up_zero, $relation_up])->flatten(1)->sortByDesc('up')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });;
                $relation_down = collect([$relation_down_zero, $relation_down])->flatten(1)->sortByDesc('down')->values()->map(function ($item, $key) {
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
}
