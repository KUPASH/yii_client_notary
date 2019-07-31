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

class ClientController extends Controller
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

        $user = Users::find()->where('id=:sessionId',[':sessionId' => $sessionId])->one();
        if(!$user || $user->type_user != Users::ROLE_CLIENT){
            return $this->redirect('/notary/notary-order');
        } else {
            return $this->render('showOrderClient', ['user' => $user]);
        }
    }
    public function actionClientOrder()
    {
        $sessionId = Yii::$app->session->get('id');
        $typeUser = Yii::$app->session->get('type_user');

        if($typeUser == Users::ROLE_CLIENT) {
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
                            $newOrder->user_id = $sessionId;
                            if ($newOrder->save()) {
                                $newFiles = new Files();
                                $newFiles->real_name_client_file = $realName;
                                $newFiles->hash_name_client_file = $filename;
                                $newFiles->short_client_key = $this->generateKey();
                                $newFiles->user_id = $sessionId;
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
        } else {
            return $this->redirect('notary/notary-order');
        }
        return $this->render('orderClient', ['model' => $model]);
    }
    public function actionDownloadClientFile()
    {
        $sessionId = Yii::$app->session->get('id');

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
        $sessionId = Yii::$app->session->get('id');

        $del = Yii::$app->request->get('del');
        $user = Users::findOne($sessionId);
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