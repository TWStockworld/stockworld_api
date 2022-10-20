<?php

namespace App\Http\Controllers;

use App\Repositories\UpdateStockRepository;
use App\Repositories\GetStockRepository;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $UpdateStockRepository;
    protected $GetStockRepository;

    public function __construct(UpdateStockRepository $UpdateStockRepository, GetStockRepository $GetStockRepository)
    {
        $this->UpdateStockRepository = $UpdateStockRepository;
        $this->GetStockRepository = $GetStockRepository;
    }
    public function update_stock_information()
    {
        return $this->UpdateStockRepository->update_stock_information();
    }
    public function update_stock_data_findmind()
    {
        return $this->UpdateStockRepository->update_stock_data_findmind();
    }
    public function update_stock_data(Request $request)
    {
        return $this->UpdateStockRepository->update_stock_data($request);
    }

    public function get_stock_category()
    {
        return $this->GetStockRepository->get_stock_category();
    }
    public function get_stock_name(Request $request)
    {
        return $this->GetStockRepository->get_stock_name($request);
    }
    public function get_stock_count()
    {
        return $this->GetStockRepository->get_stock_count();
    }
    public function get_stock(Request $request)
    {
        return $this->GetStockRepository->get_stock($request);
    }
    public function get_bulletin()
    {
        return $this->GetStockRepository->get_bulletin();
    }
    public function get_stock_special_kind(Request $request)
    {
        return $this->GetStockRepository->get_stock_special_kind($request);
    }
    public function get_stock_special_kind_detail(Request $request)
    {
        return $this->GetStockRepository->get_stock_special_kind_detail($request);
    }
    public function cal_all_stock_probability(Request $request)
    {
        return $this->GetStockRepository->cal_all_stock_probability($request);
    }
    public function cal_stock(Request $request)
    {
        return $this->GetStockRepository->cal_stock($request);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
