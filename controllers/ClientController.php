<?php

namespace app\controllers;

use app\models\Files;
use app\models\NotaryForm;
use app\models\OrderForm;
use app\models\Orders;
use app\models\Users;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

class ClientController extends \app\controllers\base\SecurityController
{
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
        $typeUser = Yii::$app->user->identity->type_user;
        if($typeUser != Users::ROLE_CLIENT) {
            return $this->redirect('/notary/notary-order');
        }
    }
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
        $orders = Orders::find()->where('user_id=:sessionId',[':sessionId' => Yii::$app->user->id])->all();
        return $this->render('showOrderClient', ['orders' => $orders]);
    }
    public function actionClientOrder()
    {
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
                if (file_exists('./uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2)) {
                    if ($model->newFile->saveAs('./uploads/' .
                        $subdirname1 . '/' .
                        $subdirname2 . '/' . $filename)
                    ) {

                        $newOrder = new Orders();
                        $newOrder->name = $model->name;
                        $newOrder->city = $model->city;
                        $newOrder->document_title = $model->documentTitle;
                        $newOrder->status = 'Waiting for response';
                        $newOrder->user_id = Yii::$app->user->id;
                        if ($newOrder->save()) {
                            $newFiles = new Files();
                            $newFiles->real_name_client_file = $realName;
                            $newFiles->hash_name_client_file = $filename;
                            $newFiles->short_client_key = $this->generateKey();
                            $newFiles->user_id = Yii::$app->user->id;
                            $newFiles->order_id = $newOrder->id;
                            if ($newFiles->save()) {
                                return $this->redirect('/client/show-order-client');
                            } else {
                                echo '<pre>';
                                print_r($newFiles->errors);
                                exit();
                            }
                        } else {
                            echo '<pre>';
                            print_r($newOrder->errors);
                            exit();
                        }
                    } else {
                        echo '<pre>';
                        print_r($model->newFile->error);
                        exit();
                    }
                }
            }
        }
        return $this->render('orderClient', ['model' => $model]);
    }
    public function actionDownloadClientFile()
    {
        $name = Yii::$app->request->get('name');
        $file = Files::find()->where('short_client_key=:name', [':name' => $name])->one();
        if (!empty($file)) {
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
        $del = Yii::$app->request->get('del');
        $user = Users::findOne(Yii::$app->user->id);
        $file = $user->getFiles()->where('order_id=:del',[':del' => $del])->one();
        if($file) {
            $fileway = './uploads/' . $file->hash_name_client_file[0] . '/' . $file->hash_name_client_file[1] . '/' . $file->hash_name_client_file;
            $order = $user->getOrders()->where('id=:del', [':del' => $del])->one();
            if ($order) {
                if ($order->status !== 'In work') {
                    if($order->delete()) {
                        unlink($fileway);
                        return $this->redirect('/client/show-order-client');
                    }
                } else {
                    Yii::$app->session->setFlash('someError', 'Order in work can not be deleted!');
                    return $this->redirect('/client/show-order-client');
                }
            }
        }
    }
}