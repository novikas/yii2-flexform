<?php
/**
 *
 * @author Novikov A
 * 
 * 	The FlexibleForm widget renders form with field (CollapsableField) that could be
 * configured as visible or not by end user. Also FlexibleForm supports templates feature
 * that allow save the form configuration and fields values.
 * 
 * You need to add widget module in your config file like following:
 * 
 * 	'modules' => [
 * 		...
 * 		'flexform' => [
 * 			'class' => 'vendor\novik\flexform\Module',
 * 		],
 * 		...
 * ],
 * 
 * Also you need to perform migration file located under the directory 'migrations' in widget root
 * 
 * What you need to provide for full widget functionality:
 * 
 *  @var Model $formModel Form model instance, that keeps fields to be rendered by widget. (Nessesary)
 *  @var string $submitTitle - string form submit button title. (Optionaly)
 *
 *	Than use this widget like ActiveForm widget, as it is a base class for this widget.
 *	
 */
namespace novikas\flexform;

use yii\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\base\Model;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use novikas\flexform\models\FlexformTemplate;
use novikas\flexform\models\FlexformField;
use novikas\flexform\assets\FlexibleFormAsset;
use novikas\interfaces\Templatable;

class FlexibleForm extends ActiveForm implements Templatable
{
	use TemplateTrait;
	const TEMPLATE_TYPE = self::TEMPLATE_FORM;
	/**
	 * 
	 * @var string field class 
	 */
	public $fieldClass = 'vendor\novik\flexform\CollapsableField';
	
	/**
	 * @var Model the data model that this form is associated with
	 */
	public $formModel;
	
	/**
	 * @var array attributes that client code intent to display
	 */
	public $displayedAttributes = [];
	
	/**
	 *
	 */
	public $submitTitle = 'Submit';
	
	/**
	 * @var array template fields
	 */
	private $fieldNames = [];
	
	/**
	 * @var Module instace of widget module  
	 */
	protected $_module;
	
	
	/**
	 * 
	 * @var string class of the model
	 */
	private $modelClass;
	
	
	
	/**
	 * (non-PHPdoc)
	 * @see \yii\widgets\ActiveForm::init()
	 */
	public function init()
	{
		Pjax::begin(['id' => 'flexform-widget-whole']);
		Pjax::begin(['id' => 'flexform-widget']);
		parent::init();
		
		if( !isset( $this->formModel ) )
			throw new InvalidConfigException("The \$formModel must be setup.");
		$this->_module = Config::initModule( Module::classname() );
		$this->modelClass = ( new \ReflectionClass( $this->formModel->className() )  )->getShortName();
		$this->template_name = $this->modelClass;
		$this->initTemplate( false );
		$this->prepareModel();
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \yii\widgets\ActiveForm::run()
	 */
	public function run()
	{	
		parent::run();
		Pjax::end();
		if( !isset( $this->template ) )
		{
			$this->createDefaultTemplate();
		}
		echo $this->render('template/footer', ['formModel' => $this->formModel, 
								'templateController' => $this->_module->templateController,
								'displayedAttributes' => $this->displayedAttributes,
								'fieldNames' => $this->template->fields,
								'submitTitle' => $this->submitTitle,
		]);
		
		Pjax::begin(['id' => 'template-panel-pjax']);
		echo $this->templatesPanel();
		Pjax::end();
		Pjax::end();
		FlexibleFormAsset::register( $this->view );
		
	}
	
	/**
	 * (non-PHPdoc)
	 * 
	 * @see \yii\widgets\ActiveForm::field()
	 */
	public function field( $model, $attribute, $options = [] )
	{
		if( isset( $attribute ) )
			$this->displayedAttributes[] = $attribute;
		
		if( isset( $this->template ) )
		{
			$options['visible'] = in_array($attribute, $this->template->fields);
		}
		else 
		{
			$options['visible'] = true;
		}
		
		return parent::field($model, $attribute, $options);		
	}
	
	/**
	 * Fills the $formModel with saved values from template fields
	 * @return boolean true if $this->template is defined and false othervise.
	 */
	private function prepareModel()
	{
		if( isset( $this->template ) )
		{
			$attr = ArrayHelper::map( $this->template->flexFields, 'name', 'value' );
			$str = 'store';
			
			foreach ( $attr as $key => $value )
			{
				if( strpos($value, ',') )
				{
					$this->formModel->$key = explode( ',', $value ); 
				}
				else
				{
					$this->formModel->$key = $value; 
				}
			}

			return true;
		}
		return false;
	}
	
	public function getDisplayedAttributes()
	{
		return $this->displayedAttributes;
	}
	
	/**
	 * Generates button group which represents templates panel
	 * @return string generated html
	 */
	private function templatesPanel()
	{
		$html = "<div id = 'template-panel-container' class = 'btn-group btn-group-justified' data-toggle= 'buttons'>";
		
		$userTemplatesList = FlexformTemplate::getUserFormTemplates( $this->modelClass );
		
		foreach ( $userTemplatesList as $key => $value )	
		{
			$html .= "<label class = 'btn btn-default template-panel current-template ".($value->current == 1?'active':'')
					."' ><input type=radio name = 'currentFilter' value = $value->id "
					.($value->current == 1?'checked':'').">$value->name</label>";
		}
		$html .="<label class = 'btn btn-default template-panel' id = 'new-template' value = $this->modelClass >
				<span class = 'glyphicon glyphicon-plus'></span>
				</label>";
		$html .= "</div>";
				
		return $html;
	}
	
}

?>

