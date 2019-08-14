<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\grid\GridView;

$this->title = 'Payment Invoice: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Paid';
?>
<div class="row">
    <!-- form pembayaran-->
    <div class="col-md-5">
        <?php $form = ActiveForm::begin(); ?>
        <?php
        echo $form->field($model, 'transaction_date')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => ''],
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ]
        ]);
        ?>
        <?= $form->field($model, 'payment_method')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'transaction_id')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'payment')->textInput(['type'=>'number']);?>

        <div class="form-group">
            <?= Html::submitButton('Paid', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <!--detail invoice-->
    <div class="col-md-7">
        <div class="well well-sm">
            <table>
                <tr>
                    <td>
                        <b>Invoiced To :</b><br>
                        <?= $model->name;?><br>
                        ATTN : <?= $model->attn;?><br>
                        <?= $model->address;?><br>
                    </td>
                    <td>&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;</td>
                    <td valign="top">
                        <b>Invoice #<?= $model->invoice_number;?></b><br>
                        Invoice Date #<?= $model->created_at;?><br>
                        Due Date #<?= $model->due_date;?><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <br>
                        <?= GridView::widget([
                            'dataProvider' => $items,
                            'layout'=>'{items}',
                            'columns' => [
                                'item',
                                [
                                    'attribute'=>'price',
                                    'content'=>function($m){
                                        return "Rp.".number_format($m->total,0);
                                    }
                                ]
                            ],
                        ]); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
