<a class="btn btn-primary" role="button" href="/notary/show-order-notary">Show my orders in work</a>
</br>
</br>
<?php
use yii\helpers\Html;
use yii\helpers\Url; ?>

<table class="table table-striped">
    <tr><th>Name</th>
        <th>City</th>
        <th>Document Title</th>
        <th>Status of document</th>
        <th>File from client</th>
        <th>Accept in work</th></tr>
    <?foreach ($orders as $order) { ?>
        <tr><td><?=$order->name?></td>
            <td><?=$order->city?></td>
            <td><?=$order->document_title?></td>
            <td><?=$order->status?></td>
            <td><a class="btn btn-primary" role="button"
                   href="<?=Url::to(['client/download-client-file', 'name' => $order->files[0]->short_client_key]);?>">Download</a></td>
            <td><a class="btn btn-success" role="button"
                   href="<?=Url::to(['take-in-work', 'work' => $order->id]);?>">Take it</a></td></tr>
    <?}?>
</table>
<?= Html::a("Logout", ['/auth/logout'], [
    'data' => ['method' => 'post'],
    'class' => 'white text-center',
]);?>