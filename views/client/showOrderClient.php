<a class="btn btn-primary" role="button" href="/client/client-order">Create new order</a>
</br>
</br>
<?php
use yii\helpers\Html;
use yii\helpers\Url; ?>
<?php if( Yii::$app->session->hasFlash('someError') ): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo Yii::$app->session->getFlash('someError'); ?>
    </div>
<?php endif;?>
<table class="table table-striped">
    <tr><th>Name</th>
        <th>City</th>
        <th>Document Title</th>
        <th>Status of document</th>
        <th>File from client</th>
        <th>File from notary</th>
        <th>Delete order</th></tr>
<?foreach ($orders as $order) { ?>
    <tr><td><?=$order->name?></td>
        <td><?=$order->city?></td>
        <td><?=$order->document_title?></td>
        <td><?=$order->status?></td>
        <td><a class="btn btn-primary" role="button"
            href="<?=Url::to(['download-client-file', 'name' => $order->files[0]->short_client_key]);?>">Download</a></td>
        <td><?if($order->files[0]->short_notary_key !== NULL) { ?><a class="btn btn-primary" role="button"
            href="<?=Url::to(['notary/download-notary-file', 'name' => $order->files[0]->short_notary_key]);?>">Download</a><?}?></td>
        <td><a class="btn btn-danger" role="button"
            href="<?=Url::to(['delete-client-order', 'del' => $order->id]);?>">Delete</a></td></tr>
<?}?>
</table>
<?= Html::a("Logout", ['/auth/logout'], [
    'data' => ['method' => 'post'],
    'class' => 'white text-center',
]);?>
