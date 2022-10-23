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

        self::tidy_cal_data($probability_up, 1);
        self::tidy_cal_data($probability_down, 2);

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


        $data = StockName::where('stock_id', $stock_id)->first();
        $relation = StockName::where('stock_id', $stock_id)->first();
        $probability_up = collect();
        $probability_down = collect();
        $relation_up = collect();
        $relation_down = collect();
        $A_B_same_category_up = collect();
        $A_B_same_category_down = collect();
        $B_category_up = collect();
        $B_category_down = collect();

        if ($stock_category_id) {
            $A_B_same_category = collect();
            $B_category = collect();

            $data = StockName::where('stock_category_id', $stock_category_id)->get();
            $all_stock_id = collect();
            $data->map(function ($item) use ($all_stock_id) {
                $all_stock_id->push($item['id']);
            });
            $data->map(function ($item) use ($A_B_same_category, $all_stock_id) {
                $item->StockCalculateStockA->map(function ($item) use ($A_B_same_category, $all_stock_id) {
                    if ($all_stock_id->contains($item['stockB_name_id'])) {
                        $A_B_same_category->push($item);
                    }
                });
            });

            $data->map(function ($item) use ($B_category) {
                $B_category->push($item->StockCalculateStockB);
            });

            $A_B_same_category = $A_B_same_category->where('stock_calculate_group_id', $stock_calculate_group_id);
            $B_category = $B_category->flatten(1)->where('stock_calculate_group_id', $stock_calculate_group_id);


            //A_B
            $A_B_same_category_up = $A_B_same_category->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->values()->take(10);
            self::tidy_cal_data($A_B_same_category_up, 1);

            $A_B_same_category_down = $A_B_same_category->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->values()->take(10);
            self::tidy_cal_data($A_B_same_category_down, 2);


            if ($show_zero_diff == 1) {
                $A_B_same_category_up_zero = $A_B_same_category->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values();
                self::tidy_cal_data($A_B_same_category_up_zero, 1);

                $A_B_same_category_down_zero = $A_B_same_category->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values();
                self::tidy_cal_data($A_B_same_category_down_zero, 2);

                $A_B_same_category_up = collect([$A_B_same_category_up_zero, $A_B_same_category_up])->flatten(1)->sortByDesc('up')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });;
                $A_B_same_category_down = collect([$A_B_same_category_down_zero, $A_B_same_category_down])->flatten(1)->sortByDesc('down')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
            }
            //全部_B

            $B_category_up = $B_category->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->values()->take(10);
            self::tidy_cal_data($B_category_up, 1);

            $B_category_down = $B_category->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->values()->take(10);
            self::tidy_cal_data($B_category_down, 2);


            if ($show_zero_diff == 1) {
                $B_category_up_zero = $B_category->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values();
                self::tidy_cal_data($B_category_up_zero, 1);

                $B_category_down_zero = $B_category->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values();
                self::tidy_cal_data($B_category_down_zero, 2);

                $B_category_up = collect([$B_category_up_zero, $B_category_up])->flatten(1)->sortByDesc('up')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
                $B_category_down = collect([$B_category_down_zero, $B_category_down])->flatten(1)->sortByDesc('down')->values()->map(function ($item, $key) {
                    unset($item['order']);
                    $item['order'] = $key + 1;
                    return $item;
                });
            }


            $probability_up = $A_B_same_category_up;
            $probability_down = $A_B_same_category_down;
            $relation_up = $B_category_up;
            $relation_down = $B_category_down;
        } else {
            if ($data != null) {
                $data = $data->StockCalculateStockA;
                $data = $data->where('stock_calculate_group_id', $stock_calculate_group_id);
                $probability_up = $data->where('sort', 1)->where('diff', '!=', 0)->sortByDesc('up')->values()->take(10);
                self::tidy_cal_data($probability_up, 1);

                $probability_down = $data->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->values()->take(10);
                self::tidy_cal_data($probability_down, 2);


                if ($show_zero_diff == 1) {
                    $probability_up_zero = $data->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values();
                    self::tidy_cal_data($probability_up_zero, 1);

                    $probability_down_zero = $data->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values();
                    self::tidy_cal_data($probability_down_zero, 2);

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
                self::tidy_cal_data($relation_up, 1);


                $relation_down = $relation->where('sort', 2)->where('diff', '!=', 0)->sortByDesc('down')->take(10)->values();
                self::tidy_cal_data($relation_down, 2);


                if ($show_zero_diff == 1) {
                    $relation_up_zero = $relation->where('diff', 0)->where('sort', 1)->sortByDesc('up')->take(5)->values();
                    self::tidy_cal_data($relation_up_zero, 1);

                    $relation_down_zero = $relation->where('diff', 0)->where('sort', 2)->sortByDesc('down')->take(5)->values();
                    self::tidy_cal_data($relation_down_zero, 2);

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
        }
        return response()->json([
            'data_start_date' => $date->startdate, 'data_end_date' => $date->enddate,
            'probability_up' => $probability_up, 'probability_down' => $probability_down,
            'relation_up' => $relation_up, 'relation_down' => $relation_down,
        ], 200);
    }

    public function tidy_cal_data($need_tidy, $sort)
    {
        if ($sort == 1) {
            $need_tidy = $need_tidy->map(function ($item, $key) {
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
        } else {
            $need_tidy = $need_tidy->map(function ($item, $key) {
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
        }
        return $need_tidy;
    }
}