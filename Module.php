<?php

namespace vendor\novik\flexform;

class Module extends \yii\base\Module
{
	const MODULE = "flexform";

	public $templateController = "flexform/template";
	public $downloadAction = 'flexform/export/download';

    public function init()
    {
        parent::init();
        
        if( isset( $this->module ) )
        	$this->templateController = $this->module->id."/".$this->templateController; 
        
        \Yii::configure($this, require(__DIR__ . '/flex_config.php'));
        //Это не обязательно если у приложения и модуля одна тема
        /*$this->layoutPath = '@app/themes/modern/layouts';
        $this->layout = 'main';*/
    }
}

?>