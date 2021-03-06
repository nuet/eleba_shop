<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodMenu;
use App\Models\Shop;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodCategoryController extends Controller
{

    //登录权限
    public function __construct(){
        //未登录的用户只能做什么
        $this->middleware('auth',['except'=>['']]);
        //让只能是未登录的用户访问的页面
//        $this->middleware('guest',['only' => ['create']]);
    }

    //菜品分类主页
    public function index(Request $request){
//        $fenye=$request->query();
//        $keyword=$request->query();
//        $wheres=[
//            ['shop_id','=',Auth::user()->shop_id],
//        ];
//        if($keyword){
//            $wheres[]=['name','like',"%{$keyword}%"];
//        }
//        $FoodCategorys=FoodCategory::where($wheres)->paginate(4);
//        return view('food.index',compact('FoodCategorys','fenye'));


        $FoodCategorys=FoodCategory::where('shop_id','=',Auth::user()->shop_id)->paginate(7);
        return view('food.index',compact('FoodCategorys'));
    }

    //>>添加菜品分类
    public function create(){
        return view('food.create');
    }

    //>>添加保存菜品分类
    public function store(Request $request){
        //基础验证
        $this->validate($request,[
            'name'=>'required',
            'description'=>'required',
        ],[
            'name.required'=>'分类名称不能为空',
            'description.required'=>'描述不能为空',
        ]);

        $shop_id=Auth::user()->shop_id;
        //保存到数据库
        FoodCategory::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'is_selected'=>0,
            'type_accumulation'=>'c'.$shop_id,
            'shop_id'=>$shop_id,
        ]);
        session()->flash('sussecc','菜品分类添加成功');
        return redirect()->route('food_category.index');
    }

    //修改回显
    public function edit(FoodCategory $FoodCategory){
        return view('food.edit',compact('FoodCategory'));
    }

    //修改保存
    public function update(Request $request,FoodCategory $FoodCategory){

        //基础验证
        $this->validate($request,[
            'name'=>'required',
            'description'=>'required',
        ],[
            'name.required'=>'分类名称不能为空',
            'description.required'=>'描述不能为空',
        ]);

        $shop_id=Auth::user()->shop_id;
        $FoodCategory->update([
            'name'=>$request->name,
            'description'=>$request->description,
            'is_selected'=>0,
            'type_accumulation'=>'c'.$shop_id,
            'shop_id'=>$shop_id,
        ]);
        session()->flash('sussecc','菜品分类修改成功');
        return redirect()->route('food_category.index');


    }

    //>>右边那个按钮--设置默认分类
    public function is_selected(FoodCategory $FoodCategory){
        DB::table('food_category')->update([
            'is_selected'=>'0',
        ]);
        $is_selected=$FoodCategory->is_selected==1?0:1;
        FoodCategory::find($FoodCategory->id)->update([
            'is_selected'=>$is_selected,
        ]);
        return redirect()->route('food_category.index');
    }

    //菜品分类删除
    public function destroy (FoodCategory $FoodCategory){
        $FoodCategory->delete();
        return redirect()->route('food_category.index');
    }

}
