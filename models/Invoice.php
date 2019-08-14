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
    const STATUS_CREATED = 'created'; //invoice diterbitkan
    const STATUS_PAID = 'paid'; //invoice dibayar
    const STATUS_CANCELED = 'canceled'; //invoice dicancel

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
            [['payment','transaction_date','payment_method','transaction_id'],'required','on'=>'payment'],
            [['due_date', 'name', 'attn', 'address', 'status', 'created_at'], 'required'],
            [['due_date', 'transaction_date', 'created_at'], 'safe'],
            [['address'], 'string'],
            [['amount', 'payment'], 'number'],
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

    public function afterSave($insert, $changedAttributes)
    {
        if($insert){
            //sebagai sample saja untuk mengenerate nomor invoice
            //format : tahun-bulan-id database
            $number = date('Y').date('m').str_pad($this->id,4,0,STR_PAD_LEFT);
            $this->updateAttributes(['invoice_number'=>$number]);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
