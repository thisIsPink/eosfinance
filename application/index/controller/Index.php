<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller{
    public function index(){
    	$this->redirect('admin/index/login');
        return '<a href="'.url('admin/index/login').'">后台</a><br/><a href="'.url('sel/index/qiantai').'">请求</a><br/><a href="'.url('api/api/api').'">api</a>';
    }
}
