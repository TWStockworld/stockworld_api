<?php

namespace App\Http\Controllers;

use App\Repositories\StockRepository;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $StockRepository;

    public function __construct(StockRepository $StockRepository)
    {
        $this->StockRepository = $StockRepository;
    }

    public function update_stock_category()
    {
        return $this->StockRepository->update_stock_category();
    }
    public function update_stock_name()
    {
        return $this->StockRepository->update_stock_name();
    }
    public function update_stock_data()
    {
        return $this->StockRepository->update_stock_data();
    }
    public function cal_stock()
    {
        return $this->StockRepository->cal_stock();
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
