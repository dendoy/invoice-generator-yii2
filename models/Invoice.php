<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $invoice_number
 * @property string $due_date
 * @property string $name
 * @property string $attn
 * @property string $address
 * @property double $amount
 * @property string $transaction_date
 * @property string $payment_method
 * @property string $transaction_id
 * @property double $payment
 * @property string $status
 * @property string $created_at
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['due_date', 'transaction_date', 'created_at'], 'safe'],
            [['address'], 'string'],
            [['amount', 'payment'], 'number'],
            [['status', 'created_at'], 'required'],
            [['invoice_number', 'transaction_id'], 'string', 'max' => 10],
            [['name', 'status'], 'string', 'max' => 15],
            [['attn'], 'string', 'max' => 30],
            [['payment_method'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'due_date' => 'Due Date',
            'name' => 'Name',
            'attn' => 'Attn',
            'address' => 'Address',
            'amount' => 'Amount',
            'transaction_date' => 'Transaction Date',
            'payment_method' => 'Payment Method',
            'transaction_id' => 'Transaction ID',
            'payment' => 'Payment',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
