<?php
namespace app\sel\model;
use think\Model;

class Img_carousel extends Model{
	public function sel_num($num){
		return db("img_carousel")->field("img_url image,herf url")->limit($num)->order('Id')->select();
	}
}
?>