<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;

?>

<div class="customer-form">

    <?php $form = \yii\widgets\ActiveForm::begin([
        'id' => 'dynamic-form',
    ]); ?>

    <?php echo $form->errorSummary($items);?>

    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'invoice_number')->textInput(['maxlength' => true,'readonly'=>true,'placeholder'=>'Number'])->label(false) ?>

            <?php
            echo $form->field($model, 'due_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Due date'],
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ]
            ])->label(false);
            ?>
        </div>
        <div class="col-md-7">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'Name'])->label(false) ?>
            <?= $form->field($model, 'attn')->textInput(['maxlength' => true,'placeholder'=>'ATTN'])->label(false) ?>
            <?= $form->field($model, 'address')->textarea(['rows' => 6,'placeholder'=>'Address'])->label(false) ?>
        </div>
    </div>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 999, // the maximum times, an element can be added (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $items[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'desciption',
            'price'
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-briefcase"></i> Items
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <?php foreach ($items as $i => $m): ?>
                    <div class="item"><!-- widgetItem -->
                        <div class="rows">
                            <?php
                            // necessary for update action.
                            if (! $m->isNewRecord) {
                                echo Html::activeHiddenInput($m, "[{$i}]id");
                            }
                            ?>
                            <table width="80%">
                                <tr>
                                    <td valign="top" width="5%"><button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button></td>
                                    <td width="70%"><?= $form->field($m, "[{$i}]item")->textInput(['maxlength' => true,'placeholder'=>'description'])->label(false) ?></td>
                                    <td width="25%">
                                        <?php echo $form->field($m, "[{$i}]total")->textInput(['placeholder'=>'price'])->label(false);?>
                                    </td>
                                </tr>
                            </table>
                        </div><!-- .row -->
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton($m->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>
</div>


