<?php

namespace novikas\flexform;

use yii\base\Model;
use yii\base\InvalidConfigException;
use kartik\grid\GridView;
use kartik\grid\GridExportAsset;
use novikas\interfaces\Templatable;
use novikas\flexform\assets;
use novikas\flexform\assets\FlexibleGridAsset;


class FlexibleGrid extends GridView implements Templatable
{
	/**
	 * 
	 */
	use TemplateTrait;
	
	/**
	 * 
	 * @var unknown
	 */
	const TEMPLATE_TYPE = self::TEMPLATE_GRID;
	
	/**
	 * this property need to be explicity setup (during configuring widget) for
	 * identifying the templates class. 
	 * @var string unique name
	 */
	public $templateName;	
	
	/**
	 * (non-PHPdoc)
	 * @see \kartik\grid\GridView::init()
	 */
	public function init()
	{
		if( !isset( $this->templateName ) )
			throw InvalidConfigException("Property tamplateName need to be set.");
		
		$this->template_name = $this->templateName;
		$this->initTemplate();
		$this->prepareColumns();
		$this->prepareToolBar();
		$this->pjax = true;
		$this->pjaxSettings['options'] =[ 'id' => 'flexible-grid-pjax' ];
		parent::init();
		
		$this->_module = Config::initModule( Module::classname() );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \kartik\grid\GridView::run()
	 */
	public function run()
	{
		parent::run();
		$view = $this->view;
		FlexibleGridAsset::register($view);
	}
	
	/**
	 * 	Preparing columns by defining value of 'visible' property.
	 * If item isn't an array method parse it, unset, and add new array item to columns array.  
	 */
	private function prepareColumns()
	{
		if( empty( $this->template ) )
		{
			throw new InvalidCallException("prepareColumns must be called only after initializing \$this->template.");
		}
		
		$attrs = $this->template->fields;
		foreach ( $this->columns as $key => $column )
		{
			if( is_array( $column ) )
			{
				if( isset( $column['attribute'] ) )
					$this->columns[$key]['visible'] = in_array($column['attribute'], $attrs);
			}
			else if( is_string($column) )
			{
				$attrName = $column;
				if ( $pos = strpos( $attrName, ':' ) )
					$attrName = in_array( substr( $attrName, $pos + 1 ) , $attrs);
		
				if( !in_array( $attrName, $attrs ) )
				{
					unset( $this->columns[$key] );
						
					$this->columns[] = [
							'attribute' => $attrName,
							'visible' => false,
					];
				}		
			}
			else 
			{
				throw new InvalidValueException("Unexpected format of columns, check if prepareColumns() method called before parent::init() call.");
			}	
		}
	}
	
	private function prepareToolBar()
	{
		$attrLabels = isset( $this->filterModel )?$this->filterModel->attributeLabels():[]; 
		
		$this->toolbar[] =[ 
					'content' => $this->render('grid-config', [
						'selected' => $this->template->fields,
						'columns' => $this->getDisplayedAttributes(),
						'attrLabels' => $attrLabels,
						'className' => $this->template_name,
						'templateId' => $this->template->id,
			])];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see novik\interfaces\Templatable::getDisplayedAttributes()
	 */
	public function getDisplayedAttributes()
	{
		$attributes = [];
		foreach ( $this->columns as $key => $column )
		{
			if( isset( $column->attribute ) )
			{
				$attributes[] = $column->attribute;
			}
			else if( isset( $column['attribute'] ) )
			{
				$attributes[] = $column['attribute'];
			}
			else if( is_string($column) )
			{
				$attrName = $column;
				if ( $pos = strpos( $attrName, ':' ) )
					$attrName = in_array( substr( $attrName, $pos + 1 ) , $attrs);
				$attributes[] = $attrName;
			} 
		}
		return $attributes;
	}
}
