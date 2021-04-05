<?php
declare (strict_types = 1);

namespace app\admin\controller;
use think\facade\View;
use think\facade\Db;
use think\facade\Cache;
use think\facade\Config;

class Banner
{
    
    public function index()
    {
        
        $template = Db::name('template')->where(['templatestatus' => 2, 'status' =>1])->cache('admin_template',Config::get('cache.expire'))->find();
        View::config(['view_path' => 'template/'.$template['template'].'/']);
        View::assign([
            'name'  => 'CMS管理系统',
            'email' => '673011635@qq.com',
            'fangwen' => number_format(169856420),
        ]);
        return View::fetch('index');
    }
    
    public function lists(){
        $page = intval(input('page'));
        $limit = intval(input('limit'));
        $data = Db::name('banner')->order('weight')->select();
        $count = Db::name('banner')->count();
        if($data){
        
            return json(['code' => 1, 'data' => $data, 'msg' => '','count' => $count]);
            
        } else{
            
           return json(['code' => 0, 'data' => '', 'msg' => '暂无数据','count' => 0]); 
        }
        
    }
    
     //修改状态
    public function status(){
        $status = input('status');
        $id = input('id');
        if($status == 1){
            $status = 2;
        } else if($status == 2){
            $status = 1;
        }
        $ok = Db::name('banner')->where('id', $id)->cache('banner',Config::get('cache.expire'))->update(['status' => $status]);
        if($ok){
            
            return json(['code' => 1, 'data' => '', 'msg' => '修改成功']);
            
        } else{
            
            return json(['code' => 0, 'data' => '', 'msg' => '修改失败，请重试']);
            
        }
    }
    
    public function add(){
            $data = input('data');
            $img_alt = $data["img_alt"];
            $url = $data["url"];
            $target = $data['target'];
            $img_src = input('img_src');
            $create_time = time();
            $datas = ['img_alt' => $img_alt, 'url' => $url, 'img_src' => $img_src, 'create_time' => $create_time,'update_time' => $create_time, 'target' => $target ];
            $ok = Db::name('banner')->insertGetId($datas);
            if($ok){ 
                Cache::delete('banner'); 
                return json(['code' => 1, 'data' => '', 'msg' => '成功' ]);
            } else{
                return json(['code' => 0, 'data' => '', 'msg' => '失败，请重试' ]);
            }
            
        }
        
    public function edit(){
        $data = input('data');
        $id = input('id');
        $updata = ['img_alt' => $img_alt, 'url' => $url, 'img_src' => $img_src, 'update_time' => $create_time, 'target' => $target, 'weight' => $data["weight"] ];
        $ok = Db::name('banner')->where('id', $id)->cache('banner',Config::get('cache.expire'))->update($updata);
        if($ok){
            
            return json(['code' => 1, 'data' => '', 'msg' => '成功' ]);
        } else{
            return json(['code' => 0, 'data' => '', 'msg' => '失败，请重试' ]);
        }
        
        
    }    
    
    //删除轮播图
    public function del($id){
        $dela = Db::name('banner')->where('id',$id)->delete();
        if($dela){
            Cache::delete('banner'); 
            return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);
        } else{
            return json(['code' => 0, 'data' => '', 'msg' => '删除失败，请重试']);
        }
        
    }
    
    
    
    
    //接收缩略图
    public function image(){
        $file = request()->file('file');
        $savename = \think\facade\Filesystem::disk('public')->putFile( 'topic', $file);
        Db::name('file')->insert(['file_route' => '/storage/'.$savename, 'creat_time' => time()]);
        return json(["code" => '1', "data" =>"", "msg" => "成功", "image" =>'/storage/'.$savename]);
    }
    
    
}