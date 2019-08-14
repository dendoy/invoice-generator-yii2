<?php
use yii\helpers\Html;
?>

<table border="0" width="100%">
    <tr>
        <td align="right">
            <?php if($model->status==\app\models\Invoice::STATUS_PAID):?>
                <?= Html::img('@web/img/paid.png');?>
            <?php else:?>
                <?= Html::img('@web/img/unpaid.jpg');?>
            <?php endif;?>
        </td>
    </tr>
</table>

<table border="0" width="100%" cellpadding="5" cellspacing="5">
    <tr>
        <td bgcolor="#a9a9a9" width="212px"> &nbsp;L O G O</td>
        <td align="right">
            Tajimalela<br>
            Sumedang -  Indonesia
        </td>
    </tr>
</table>
<br><br>
<table width="100%" cellpadding="5" cellspacing="5">
    <tr>
        <td bgcolor="#eee">
            <b>Invoice #<?= $model->invoice_number;?></b><br>
            Invoice Date #<?= $model->created_at;?><br>
            Due Date #<?= $model->due_date;?><br>
        </td>
    </tr>

    <tr>
        <td>
            <br><br>
            <b>Invoiced To :</b><br>
            <?= $model->name;?><br>
            ATTN : <?= $model->attn;?><br>
            <?= $model->address;?><br>
        </td>
    </tr>

    <tr>
        <td>
            <br><br>
            <table width="100%" cellpadding="5" cellspacing="0" border="1">
                <tr bgcolor="#eee">
                    <th width="80%" align="center">Description</th>
                    <th align="center">Total</th>
                </tr>
                <?php foreach($items as $m):?>
                    <tr>
                        <td><?= $m->item;?></td>
                        <td>Rp.<?= number_format($m->total,0);?></td>
                    </tr>
                <?php endforeach;?>

                <tr bgcolor="#eee">
                    <td align="right">
                        <b>Subtotal</b><br>
                        <b>Credit</b>
                    </td>
                    <td align="center">
                        <b>Rp.<?= number_format($model->amount,0);?></b><br>
                        <b>Rp.0</b>
                    </td>
                </tr>
                <tr bgcolor="#eee">
                    <td align="center">
                        <b>Total</b>
                    </td>
                    <td align="center">
                        <b>Rp.<?= number_format($model->amount,0);?></b>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

<br>

<h3>Transaction</h3>
<table width="100%" cellpadding="5" cellspacing="0" border="1">
    <tr bgcolor="#eee">
        <th align="center">Transaction Date</th>
        <th align="center">Payment Method</th>
        <th align="center">Transaction ID</th>
        <th align="center">Amount</th>
    </tr>
    <?php if($model->status=='UNPAID'):?>
        <tr>
            <td colspan="4" align="center">No Related Transactions Found</td>
        </tr>
        <tr bgcolor="#eee">
            <td colspan="3" align="right"><b>Balance</b></td>
            <td align="center"><b>Rp.<?= number_format($model->amount,0);?></b></td>
        </tr>
    <?php else:?>
        <tr>
            <td align="center"><?= $model->transaction_date;?></td>
            <td align="center"><?= $model->payment_method;?></td>
            <td align="center"><?= $model->transaction_id;?></td>
            <td align="center">Rp.<?= number_format($model->payment,0);?></td>
        </tr>
        <tr bgcolor="#eee">
            <td colspan="3" align="right"><b>Balance</b></td>
            <td align="center"><b>Rp.0</b></td>
        </tr>

    <?php endif;?>
</table>
<br><br><br><br>
<table width="100%">
    <tr>
        <td align="center">PDF Generated on <?= $model->created_at;?></td>
    </tr>
</table>