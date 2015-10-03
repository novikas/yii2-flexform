<?php
namespace vendor\novik\flexform\assets;

use yii\web\AssetBundle;

class FlexibleGridAsset extends AssetBundle
{
	public $basePath = '@flex/root/assets/';
	public $baseUrl = '@flex/root/assets/';

	public $js = [
			'flexgrid.js',
	];
}