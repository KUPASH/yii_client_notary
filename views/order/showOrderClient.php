<a class="btn btn-primary" role="button" href="/order/client-order">Create new order</a>
</br>
</br>
<?php
use yii\helpers\Url; ?>

<table class="table table-striped">
    <tr><th>Name</th>
        <th>City</th>
        <th>Document Title</th>
        <th>Status of document</th>
        <th>File from client</th>
        <th>File from notary</th>
        <th>Delete order</th></tr>
<?foreach ($user->orders as $order) { ?>
    <tr><td><?=$order->name?></td>
        <td><?=$order->city?></td>
        <td><?=$order->document_title?></td>
        <td><?=$order->status?></td>
        <td><a class="btn btn-primary" role="button"
            href="<?=Url::to(['download-client-file', 'name' => $order->files[0]->short_client_key]);?>">Download</a></td>
        <td><a class="btn btn-primary" role="button"
            href="<?=Url::to(['download-client-file', 'name' => $order->files[0]->short_client_key]);?>">Download</a></td>
        <td><a class="btn btn-danger" role="button"
            href="<?=Url::to(['delete-client-order', 'del' => $order->id]);?>">Delete</a></td></tr>
<?}?>
    </table>
<?php if( Yii::$app->session->hasFlash('success') ): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif;?>
