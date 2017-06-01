<?php
namespace bignerd\extension;
use yii\web\AssetBundle;
use yii;

class UploadfiveAsset extends AssetBundle
{
	public $sourcePath = '@uploadifive/assets';
	public $basePath = '@webroot/assets';
	public $js = [
		'uploadifive/jquery.uploadifive.min.js',
	];
	public $css = [
		'uploadifive/uploadifive.css',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init() {
		Yii::setAlias('@uploadifive', __DIR__);
		return parent::init();
	}
}
?>