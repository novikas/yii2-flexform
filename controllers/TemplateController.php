<?php

namespace novikas\flexform\controllers;

use yii\base\Controller;
use yii\db\Exception;

use novikas\flexform\models\FlexformField;
use novikas\flexform\models\FlexformTemplate;
use novikas\interfaces\Templatable;

class TemplateController extends Controller
{
	public function actionAddField()
	{	
		if( isset($_POST['templateId']) && isset( $_POST['fieldName'] ) )
		{
			$field = new FlexformField();
			
			$field->name = $_POST['fieldName'];
			$field->FK_template = $_POST['templateId'];
			$field->order = 1;
			
			if( !$field->save() )
			{
				throw new Exception('Error occured during creating field.\n'.print_r( $field->getErrors(), true ));
			}
		}
		return 'field'.$_POST['fieldName'].' to template '.$_POST['templateId'].' added';
	}
	
	public function actionRemoveField()
	{
		if( isset($_POST['templateId']) && isset( $_POST['fieldName'] ) )
		{
			$field = FlexformField::find()
					->where(['FK_template' => $_POST['templateId'], 
							'name' => $_POST['fieldName']])->one();
			$field->delete();
		}
		return 'field'.$_POST['fieldName'].' from template '.$_POST['templateId'].' removed';
	}
	
	public function actionCreateTemplate()
	{
		$model = new FlexformTemplate();
		
		$model->setAttributes(\Yii::$app->request->post());
		$model->FK_user = \Yii::$app->user->identity->id;
		$model->type = 1;
		$model->current = 1;
		$model->public = 0;
		
		if ( $model->save() )
		{
			$model->refresh();
			$model->setCurrent();
		}
		
		return json_encode( [ 'status' => 1 ] );
	}

	
	public function actionSetCurrentTemplate()
	{
		$model = FlexformTemplate::findOne( $_POST['id'] );
		
		if( $model != null )
		{
			$model->setCurrent();
		}
		
		return json_encode([ 'status' => 1, 'current' => $model->id ]);
	}	
	
	/**
	 * 
	 * @return string
	 */
	public function actionRemoveTemplate(){
		$model = FlexformTemplate::findOne( $_POST['id'] );
		if( isset( $model ) )
		$model->delete();
		return json_encode([ 'status' => 1 ]);
	}
	
	/**
	 * Method saves to template given values 
	 * @throws InvalidValueException
	 * @return mixed json encoded array with status 
	 */	
	public function actionSaveTemplate()
	{	
		if( !isset($_POST['templateId']) )
		{
			throw new InvalidValueException("Template id missing.");	
		}
		
		$model = FlexformTemplate::findOne($_POST['templateId']);
		
		foreach ( \Yii::$app->request->post($model->modelClass, null) as $key => $value )
		{
			$field = FlexformField::find()->where([ 'name' => $key, 'FK_template' => $_POST['templateId'] ])->one();
			if( !isset($field) )
				continue;
			
			if( is_array( $value ) )
			{
				$field->value = implode( ',', $value );
			}
			else 
			{
				$field->value = $value;
			}
			
			$field->save();
		}
		
		return json_encode(['status' => 1 ]);
	}
	
}
