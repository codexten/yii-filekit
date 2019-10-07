<?php

namespace codexten\yii\filekit\actions;

use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class UploadAction extends \trntv\filekit\actions\UploadAction
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $result = [];
        $uploadedFiles = UploadedFile::getInstancesByName($this->fileparam);

        foreach ($uploadedFiles as $uploadedFile) {
            /* @var \yii\web\UploadedFile $uploadedFile */
            $output = [
                $this->responseNameParam => Html::encode($uploadedFile->name),
                $this->responseMimeTypeParam => $uploadedFile->type,
                $this->responseSizeParam => $uploadedFile->size,
                $this->responseBaseUrlParam => $this->getFileStorage()->baseUrl,
            ];
            if ($uploadedFile->error === UPLOAD_ERR_OK) {
                $validationModel = DynamicModel::validateData(['file' => $uploadedFile], $this->validationRules);
                if (!$validationModel->hasErrors()) {
                    $path = $this->getFileStorage()->save($uploadedFile, false, false, $this->saveConfig,
                        $this->uploadPath);

                    if ($path) {
                        $path = ltrim($path, '/');
                        $output[$this->responsePathParam] = $path;
                        $output[$this->responseUrlParam] = $this->getFileStorage()->baseUrl.'/'.$path;
                        $output[$this->responseDeleteUrlParam] = Url::to([$this->deleteRoute, 'path' => $path]);
                        $paths = \Yii::$app->session->get($this->sessionKey, []);
                        $paths[] = $path;
                        \Yii::$app->session->set($this->sessionKey, $paths);
                        $this->afterSave($path);

                    } else {
                        $output['error'] = true;
                        $output['errors'] = [];
                    }

                } else {
                    $output['error'] = true;
                    $output['errors'] = $validationModel->getFirstError('file');
                }
            } else {
                $output['error'] = true;
                $output['errors'] = $this->resolveErrorMessage($uploadedFile->error);
            }

            $result['files'][] = $output;
        }
        return $this->multiple ? $result : array_shift($result);
    }
}