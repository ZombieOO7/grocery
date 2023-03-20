<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductHelper extends BaseHelper
{

    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get Category list                                   |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->product::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | comapny detail by id                               |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->product::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | Category store                                      |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $request['status']= 1;
            $product = $this->product::findOrFail($request->id);
        } else {
            $product = new Product();
        }
        $product->fill($request->all())->save();
        if($request->hasFile('images')){
            if(count($product->productMedia)>0){
                $product->productMedia()->delete();
            }
            foreach($request->images as $image){
                $productMedia = new ProductMedia();
                $productMedia->product_id = $product->id;
                $productMedia->save();
                $attachment = $productMedia->attachment;
                $productMedia->updateAttachment($image,$attachment);
            }
        }
        return $product;
    }

    /**
     * ------------------------------------------------------
     * | Update status                                      |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function statusUpdate($uuid)
    {
        $product = $this->detail($uuid);
        $status = $product->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->product::where('id', $product->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Product']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | Category detail by uuid                             |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->product::where('uuid', $uuid)->with('productMedia')->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete Category                                     |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $product = $this->detail($uuid);
        $product->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple Category                                     |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $product = $this->product::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $product->delete();
        }else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $product->update(['status' => $status]);
        }
    }
}
