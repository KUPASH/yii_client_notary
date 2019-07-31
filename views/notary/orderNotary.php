<a class="btn btn-primary" role="button" href="/notary/notary-order">Return to all orders</a>
</br>
</br>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm; ?>
<table class="table table-striped">
    <tr><th>Name</th>
        <th>City</th>
        <th>Document Title</th>
        <th>Status of document</th>
        <th>File from client</th>
        <th>File from notary</th>
        <th>Send to the client</th></tr>
    <?foreach ($orders as $order) { ?>
        <tr><td><?=$order->name?></td>
            <td><?=$order->city?></td>
            <td><?=$order->document_title?></td>
            <td><?=$order->status?></td>
            <td><a class="btn btn-primary" role="button"
                   href="<?=Url::to(['client/download-client-file', 'name' => $order->files[0]->short_client_key]);?>">Download</a></td>
            <td><a class="btn btn-primary" role="button"
                   href="<?=Url::to(['upload-file-notary', 'edit' => $order->id]);?>">Upload file</a>
                <a class="btn btn-primary" role="button"
                   href="<?=Url::to(['download-notary-file', 'name' => $order->files[0]->short_notary_key]);?>">Download</a></td>
            <td><a class="btn btn-success" role="button"
                   href="<?=Url::to(['done-order-by-notary', 'done' => $order->id]);?>">Done</a></td></tr>
    <?}?>
</table>
<?= Html::a("Logout", ['/auth/logout'], [
    'data' => ['method' => 'post'],
    'class' => 'white text-center',
]);?>

