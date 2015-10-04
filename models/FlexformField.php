<?php

namespace novikas\flexform\models;

use yii\db\ActiveRecord;
/**
 * This is the model class for table "flexform_field".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property integer $FK_template
 * @property integer $order
 *
 * @property FlexformTemplate $template
 */
class FlexformField extends ActiveRecord
{
	public static function tableName()
	{
		return 'flexform_field';
	}

	public function rules()
	{
		return [
				[['name', 'FK_template'], 'required'],
				[['FK_template'], 'integer'],
				[['name', 'value'], 'string'],
		];
	}
	
	public function getTemplate()
	{
		return $this->hasOne( FlexformTemplate::className(), [ 'id' => 'FK_template' ] );
	}

}

?>
