<?php

namespace App\Admin\Controllers;

use App\Models\Bulletin;
use App\Models\StockName;
use App\Models\StockSpecialKind;
use App\Models\StockSpecialKindDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StockSpecialKindDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'StockSpecialKindDetail';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StockSpecialKindDetail());

        $grid->column('id', __('Id'));
        $grid->column('Bulletin.title', __('Bulletin id (title)'));
        $grid->column('StockSpecialKind.title', __('Stock special kind id (title)'));
        $grid->column('StockName.stock_name', __('Stock name '));
        $grid->column('StockName.stock_id', __('Stock_id '));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(StockSpecialKindDetail::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bulletin_id', __('Bulletin id'));
        $show->field('stock_special_kind_id', __('Stock special kind id'));
        $show->field('stock_name_id', __('Stock name id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StockSpecialKindDetail());

        $form->select('bulletin_id')->options(Bulletin::all()->pluck('title', 'id'));
        $form->select('stock_special_kind_id')->options(StockSpecialKind::all()->pluck('title', 'id'));
        $form->select('stock_name_id')->options(StockName::all()->pluck('stock_id', 'id'));

        return $form;
    }
}
