<?php
namespace app\admin\model;
use think\Model;
use think\DB;
class Super_apply extends Model{
    protected $pk='Id';
    public function getAll($name,$where,$order,$page=[1,20]){
        $data=Super_apply::where($where)->field($name)->order($order)->page($page[0],$page[1])->select();
        $num=Super_apply::where($where)->count();
        return [$data,$num];
    }
    public function up($id,$data){
        return Super_apply::where('Id',$id)->update($data);
    }
    public function getInfo($id){
        return Super_apply::find($id);
    }
    public function add($data){
        return Super_apply::insert($data);
    }
}
?>