<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice_item".
 *
 * @property int $id
 * @property int $id_invoice
 * @property string $item
 * @property double $total
 */
class InvoiceItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item', 'total'], 'required'],
            [['id_invoice'], 'integer'],
            [['item'], 'string'],
            [['total'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_invoice' => 'Id Invoice',
            'item' => 'Item',
            'total' => 'Total',
        ];
    }
}
