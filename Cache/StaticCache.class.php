<?php 
//设置静态缓存
class StaticCahce{
	private $_dir = 'cache';
	private $ext = '.txt';

	public function cache($key, $value=''){
		//文件缓存路径
		$filename = $this->_dir.'/'.$key.$this->ext;

		//写入缓存
		if ($value !== '') {
			//删除缓存
			if (is_null($value)) {
				return unlink($filename);
			}
			if (!is_dir($this->_dir)) {
				mkdir($this->_dir,0777);
			}

			return file_put_contents($filename, json_encode($value));
		}

		//读取缓存
		if (is_file($filename)) {
			$file = file_get_contents($filename);
			return json_decode($file,true);
		}

		return false;
		
	}
}
