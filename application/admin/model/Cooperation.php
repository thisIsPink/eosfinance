<?php
namespace app\admin\model;
use think\Model;
use think\DB;
class Cooperation extends Model{
    protected $pk='Id';
    public function getAll($name,$where,$order,$page=[1,20]){
        $data=Cooperation::where($where)->field($name)->order($order)->page($page[0],$page[1])->select();
        $num=Cooperation::where($where)->count();
        return [$data,$num];
    }

}
?>