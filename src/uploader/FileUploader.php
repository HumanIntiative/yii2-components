<?php

namespace pkpudev\components\uploader;

use creocoder\flysystem\Filesystem;
use yii\base\Component;
use yii\db\ActiveRecordInterface;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class FileUploader extends Component
{
    /**
     * @var string Ex: /document/ipp_attachment
     */
    public $targetDir;
    /**
     * @var string Ex: Lampiran IPP
     */
    public $type;
    /**
     * @var FileSystem
     */
    public $fileSystem;
    /**
     * @var bool
     */
    public $isOneInstance = true;
    /**
     * @var ActiveRecordInterface
     */
    protected $model;
    /**
     * @var string
     */
    protected $attribute;
    /**
     * @var UploadedFile[]
     */
    protected $files;
    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @param ActiveRecordInterface $model
     * @param string $attribute
     * @param bool $isOneInstance
     */
    public function __construct(ActiveRecordInterface $model, $attribute, $isOneInstance=true)
    {
        $this->model         = $model;
        $this->attribute     = $attribute;
        $this->isOneInstance = $isOneInstance;

        if ($this->isOneInstance) {
            $file = UploadedFile::getInstance($this->model, $this->attribute);
            $this->files = [$file];
            $this->isValid = $file instanceof UploadedFile;
        } else {
            $this->files = UploadedFile::getInstances($this->model, $this->attribute);
            $this->files = $this->validateArray($this->files);
            $this->isValid = is_array($this->files) && ($this->files[0] instanceof UploadedFile);
        }
    }

    public function setFiles($files)
    {
        $this->files = $this->validateArray($files);
        $this->isValid = is_array($this->files) && ($this->files[0] instanceof UploadedFile);
    }

    protected function validateArray($array)
    {
        return array_filter($array, function ($f) { 
            return $f instanceof UploadedFile;
        });
    }

    /**
     * @return bool Is valid file uploaded
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * @param string $prefix
     * @param FileModelInterface $fileModel
     */
    public function upload($prefix, FileModelInterface $fileModel)
    {
        // Or maybe throw Exception
        if (!$this->isValid || empty($this->targetDir)) {
            throw new \yii\base\InvalidConfigException("Error Init FileUploader, {$this->attribute}");
        }

        $retval = false;
        foreach ($this->files as $uploaded) {
            if ($uploaded->error != UPLOAD_ERR_OK) continue;

            $stream = fopen($uploaded->tempName, 'r+');
            $filename = "{$prefix}_{$uploaded->name}";
            $fullpath = "{$this->targetDir}/{$filename}";
            $result = $this->fileSystem->writeStream($fullpath, $stream); //write or update
            fclose($stream);

            if ($retval = $this->fileSystem->fileExists($fullpath)) {
                $retval = $fileModel->saveFile($uploaded, $filename, $this->targetDir, $this->type);
            } else {
                throw new ServerErrorHttpException("Gagal menyimpan file", 500);
            }
        }
        return $retval;
    }
}