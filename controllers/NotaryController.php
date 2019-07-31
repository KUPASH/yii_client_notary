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

class NotaryController extends Controller
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
    public function actionNotaryOrder()
    {
        $sessionId = Yii::$app->session->get('id');
        $typeUser = Yii::$app->session->get('type_user');
        if($typeUser == Users::ROLE_NOTARY) {
            $orders = Orders::find()->where(['status' => 'Waiting for response'])->all();
            return $this->render('showAllOrdersForNotary', ['orders' => $orders]);
        } else {
            return $this->redirect('/client/show-order-client');
        }
    }
    public function actionTakeInWork()
    {
        $sessionId = Yii::$app->session->get('id');
        $typeUser = Yii::$app->session->get('type_user');
        if ($typeUser == Users::ROLE_NOTARY) {
            $work = Yii::$app->request->get('work');
            $order = Orders::find()->where('id=:work', [':work' => $work])->one();
            if ($order) {
                if ($order->status == 'Waiting for response') {
                    $order->notary_id = $sessionId;
                    $order->status = 'In work';
                    if ($order->save()) {
                        return $this->redirect('/notary/show-order-notary');
                    } else {
                        echo '<pre>';
                        print_r($order->errors);
                        exit();
                    }
                }
            }
        } else {
            return $this->redirect('/client/show-order-client');
        }
    }
    public function actionShowOrderNotary()
    {
        $sessionId = Yii::$app->session->get('id');
        $typeUser = Yii::$app->session->get('type_user');
        if($typeUser == Users::ROLE_NOTARY) {
            $orders = Orders::find()->where('notary_id=:sessionId', [':sessionId' => $sessionId])->all();
            return $this->render('orderNotary', ['orders' => $orders]);
        } else {
            return $this->redirect('/client/show-order-client');
        }
    }
    public function actionUploadFileNotary()
    {
        $typeUser = Yii::$app->session->get('type_user');
        if($typeUser == Users::ROLE_NOTARY) {
            $model = new NotaryForm();
            if ($model->load(Yii::$app->request->post())) {
                $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');
                if ($model->pdfFile && $model->validate()) {
                    $realName = $model->pdfFile->baseName . '.' . $model->pdfFile->extension;
                    $filename = md5(time() . rand(1, 9999) . $model->pdfFile->baseName) . '.' . $model->pdfFile->extension;
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
                        if ($model->pdfFile->saveAs('./uploads/' .
                            $subdirname1 . '/' .
                            $subdirname2 . '/' . $filename)
                        ) {

                            $edit = Yii::$app->request->get('edit');
                            $file = Files::find()->where('order_id=:edit', [':edit' => $edit])->one();
                            if ($file) {
                                $file->real_name_notary_file = $realName;
                                $file->hash_name_notary_file = $filename;
                                $file->short_notary_key = $this->generateKey();
                                if ($file->save()) {
                                    return $this->redirect('/notary/show-order-notary');
                                } else {
                                    echo '<pre>';
                                    print_r($file->errors);
                                    exit();
                                }
                            }
                        } else {
                            echo '<pre>';
                            print_r($model->pdfFile->error);
                            exit();
                        }
                    }
                }
            }
        } else {
            return $this->redirect('/client/show-order-client');
        }
        return $this->render('uploadNotaryFile', ['model' => $model]);
    }
    public function actionDownloadNotaryFile()
    {
        $sessionId = Yii::$app->session->get('id');

        $name = Yii::$app->request->get('name');
        $file = Files::find()->where('short_notary_key=:name',[':name' => $name])->one();
        if(!empty($file)) {
            $realName = $file->real_name_notary_file;
            $fileway = './uploads/' . $file->hash_name_notary_file[0] . '/' . $file->hash_name_notary_file[1] . '/' . $file->hash_name_notary_file;
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
    public function actionDoneOrderByNotary()
    {
        $sessionId = Yii::$app->session->get('id');
        $typeUser = Yii::$app->session->get('type_user');
        if($typeUser == Users::ROLE_NOTARY) {
            $done = Yii::$app->request->get('done');
            $order = Orders::find()->where('id=:done', [':done' => $done])->one();
            if ($order) {
                $order->status = 'Done';
                if ($order->save()) {
                    return $this->redirect('/notary/show-order-notary');
                } else {
                    echo '<pre>';
                    print_r($order->errors);
                    exit();
                }
            }
        } else {
            return $this->redirect('/client/show-order-client');
        }
    }
}