<?php

namespace novikas\flexform;

use yii\widgets\ActiveField;
use yii\helpers\Html;

class CollapsableField extends ActiveField
{

	/**
     * @var string the template that is used to arrange the label, the input field, the error message and the hint text.
     * The following tokens will be replaced when [[render()]] is called: `{label}`, `{input}`, `{error}` and `{hint}`.
     */
    public $template = "{label}\n{hider}\n{input}\n{hint}\n{error}";
	/*
	*	Кнопка скрывающяя поле
	*/
	public $hideButton = "<a href='#' style = 'margin-top:2px;margin-bottom:2px;
	float: right;padding-right: 4px;padding-left: 5px;padding-top: 2px;padding-bottom: 0px'
	 class = 'btn btn-default put-on-cloak'><span style = 'color:#C0C0C0' class = 'glyphicon glyphicon-remove' ></a>";

	public $visible;
	
	public function begin()
	{
		$this->inputOptions['placeholder'] = 'Choose';
		$control = parent::begin()."<span class = 'cloak' id = '"
					.$this->attribute."-cloak'".($this->visible?'':'hidden').">";
		
		return $control;
	}

	public function end()
	{
		return "</span>".parent::end();
	}

	public function render( $content = null )
	{
		if (!isset($this->parts['{hider}'])) {
            $this->parts['{hider}'] = "<a href='#' style = 'margin-top:2px;margin-bottom:2px;
		float: right;padding-right: 4px;padding-left: 5px;padding-top: 2px;padding-bottom: 0px'
	 	class = 'btn btn-default put-on-cloak' value = $this->attribute><span style = 'color:#C0C0C0' class = 'glyphicon glyphicon-remove' ></a>";
        }
        return parent::render();
	}

}

?>
