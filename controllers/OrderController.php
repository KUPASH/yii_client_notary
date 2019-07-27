<?php

namespace app\controllers;

use app\models\Files;
use app\models\OrderForm;
use app\models\Orders;
use app\models\Users;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

class OrderController extends Controller
{
    private function generateKey()
    {
        $shortKey = '';
        $keyLength = 8;
        for($i=0; $i<$keyLength; $i++) {
            $shortKey .= chr(mt_rand(33,126));
        }
        return $shortKey;
    }
    public function actionShowOrderClient()
    {
        $sessionId = Yii::$app->session->get('id');
        $sessionName = Yii::$app->session->get('login');

        $user = Users::findOne($sessionId);
        $orders = $user->orders;

        return $this->render('showOrderClient', ['user' => $user]);
    }
    public function actionClientOrder()
    {
        $sessionId = Yii::$app->session->get('id');
        $sessionName = Yii::$app->session->get('login');
        $type = Yii::$app->session->get('type_user');

        $model = new OrderForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->newFile = UploadedFile::getInstance($model, 'newFile');
            if ($model->newFile && $model->validate()) {
                $realName = $model->newFile->baseName . '.' . $model->newFile->extension;
                $filename = md5(time() . rand(1, 9999) . $model->newFile->baseName) . '.' . $model->newFile->extension;
                $subdirname1 = $filename[0];
                $subdirname2 = $filename[1];

                if (!file_exists('./uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2)
                ) {
                    mkdir('./uploads/' .
                        $subdirname1 . '/' .
                        $subdirname2, 0777, true);
                }
                $model->newFile->saveAs('./uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2 . '/' . $filename);

                $newOrder = new Orders();
                $newOrder->name = $model->name;
                $newOrder->city = $model->city;
                $newOrder->document_title = $model->documentTitle;
                $newOrder->status = 'Waiting for response';
                $newOrder->user_id = $sessionId;
                $newOrder->save();

                $newFiles = new Files();
                $newFiles->real_name_client_file = $realName;
                $newFiles->hash_name_client_file = $filename;
                $newFiles->short_client_key = $this->generateKey();
                $newFiles->user_id = $sessionId;
                $newFiles->order_id = $newOrder->id;
                $newFiles->save();
                if($newOrder->save() && $newFiles->save()) {
                    return $this->redirect('/order/show-order-client');
                }
            }
        }
        return $this->render('orderClient', ['model' => $model]);
    }
    public function actionDownloadClientFile()
    {
        $sessionId = Yii::$app->session->get('id');
        $sessionName = Yii::$app->session->get('login');
        $name = Yii::$app->request->get('name');
        $user = Users::findOne($sessionId);
        $file = $user -> getFiles()->where('short_client_key=:name',[':name' => $name])->one();
        if(!empty($file)) {
            $realName = $file->real_name_client_file;
            $fileway = './uploads/' . $file->hash_name_client_file[0] . '/' . $file->hash_name_client_file[1] . '/' . $file->hash_name_client_file;
            if (file_exists($fileway)) {
                header('Content-Description: File Transfer');
                header('Content-Type: octet-stream');
                header('Content-Disposition: attachment; filename="' . $realName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileway));
                readfile($fileway);
            }
        }
    }
    public function actionDeleteClientOrder()
    {
        $sessionId = Yii::$app->session->get('id');
        $sessionName = Yii::$app->session->get('login');
        $del = Yii::$app->request->get('del');
        $user = Users::findOne($sessionId);
        $file = $user->getFiles()->where('order_id=:del',[':del' => $del])->one();
        $fileway = './uploads/' . $file->hash_name_client_file[0] . '/' . $file->hash_name_client_file[1] . '/' . $file->hash_name_client_file;
        $order = $user->getOrders()->where('id=:del',[':del' => $del])->one();
        if($order->status !== 'In work') {
            $order -> delete();
            unlink($fileway);
            return $this->redirect('/order/show-order-client');
        } else {
            Yii::$app->session->setFlash('error', 'Order in work can not be deleted!');
            return $this->redirect('/order/show-order-client');
        }
    }
    public function actionNotaryOrder()
    {
        $sessionId = Yii::$app->session->get('id');
        $sessionName = Yii::$app->session->get('login');
        $type = Yii::$app->session->get('type_user');

        return 'Hi, dear '.$sessionName.' notary!';
        return $this->render('orderNotary');
    }


}