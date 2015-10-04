<?php
/**
 *
 * @author Novikov A
 * 
 * 
 *	
 */
namespace novikas\flexform;

use yii\base\InvalidConfigException;
use yii\base\InvalidCallException;
use yii\db\Exception;


use novikas\flexform\models\FlexformTemplate;
use novikas\flexform\models\FlexgridTemplate;
use novikas\flexform\models\FlexformField;

use novikas\interfaces\Templatable;

trait TemplateTrait
{
	/**
	 * Need to be set before call any trait's method.
	 * Unique name of template
	 * @var string
	 */	
	private $template_name;	
	
	/**
	 * 
	 * @var Template instance or it's descedant
	 */
	private $template;
	
	/**
	 * Initializing template property. 
	 * 
	 * In all success cases $this->template property will be setted.
	 * 
	 * @return Template instance or it's descedant 
	 * 
	 */
	public function initTemplate( $createDefault = true )
	{
		$this->template = $this->getCurrentTemplate();
		if( isset( $this->template ) )
		{
			return $this->template;
		}
		return $createDefault?$this->createDefaultTemplate():null;
		
	}
	
	/**
	 * 
	 * @return Ambigous <unknown, boolean>|Ambigous <\yii\db\static, boolean>|boolean
	 */
	private function getCurrentTemplate()
	{
		if( $this instanceof Templatable )
		{
			$class = get_called_class();
			switch( $class::TEMPLATE_TYPE )
			{
				case $class::TEMPLATE_GRID:
					return FlexgridTemplate::getCurrentFor($this->template_name);
				break;
						
				case $class::TEMPLATE_FORM:
					return FlexformTemplate::getCurrentFor($this->template_name);
				break;
			}
			return false;
		}
		else
		{
			throw InvalidCallException('Class that uses TemplateTrait have to implement TemplatableInterface');	
		}
	}
	
	/**
	 * Creates and set default template for given $this->formModel.
	 * 
	 * @throws Exception database exception if creating fails
	 * @return Template instance or it's descedant
	 */
	private function createDefaultTemplate()
	{
		$class = get_called_class();
		$template;
		switch ( $class::TEMPLATE_TYPE )
		{
			case $class::TEMPLATE_FORM:
				$template = new FlexformTemplate();
			break;

			case $class::TEMPLATE_GRID:
				$template = new FlexgridTemplate();
			break;
		}
		
		$template->name = 'Default';
		$template->public = 1;
		$template->type = $class::TEMPLATE_TYPE;
		$template->FK_user = \Yii::$app->user->identity->id;
		$template->modelClass = $this->template_name;
		$template->current = 1;
		
		if( !$template->save() )
		{
			throw new Exception('Database error during creating deafault template.\n'.print_r( $template->getErrors(), true ) );
		}
		
		$template->refresh();
		$template->setCurrent();
		
		foreach ( $this->getDisplayedAttributes() as $key => $value )
		{
			$tempField = new FlexformField();
				
			$tempField->FK_template = $template->id;
			$tempField->name = $value;
			$tempField->order = $key;
				
			$tempField->save();
		}
		
		return $this->template = $template;
	}
	
}

?>

