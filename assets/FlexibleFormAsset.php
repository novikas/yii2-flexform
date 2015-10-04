<?php
namespace novikas\flexform\assets;

use yii\web\AssetBundle;

class FlexibleFormAsset extends AssetBundle
{
	public $basePath = '@flex/root/assets/';
	public $baseUrl = '@flex/root/assets/';
	
	public $js = [
		'flexform.js',	
	];
}
