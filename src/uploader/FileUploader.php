<?php

namespace pkpudev\components\uploader;

use creocoder\flysystem\Filesystem;
use yii\base\Component;
use yii\db\ActiveRecordInterface;
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
	 * @var string
	 */
	protected $webroot;

	public function __construct(ActiveRecordInterface $model, string $attribute)
	{
		$this->model      = $model;
		$this->attribute  = $attribute;

		$this->webroot = \Yii::getAlias('webroot');
		if ($this->isOneInstance) {
			$file = UploadedFile::getInstance($this->model, $this->attribute);
			$this->files = [$this->file];
			$this->isValid = $file instanceof UploadedFile;
		} else {
			$this->files = UploadedFile::getInstances($this->model, $this->attribute);
			$this->isValid = is_array($this->files) && ($this->files[0] instanceof UploadedFile);
		}
	}

	public function upload(string $prefix, FileModelInterface $fileModel)
	{
		// Or maybe throw Exception
		if (!$this->isValid || empty($this->targetDir))
			throw new \yii\base\InvalidConfigException("Error Initialize FileUploader");

		$retval = true;
		foreach ($this->files as $uploaded) {
			if ($uploaded != UPLOAD_ERR_OK) continue;

			$stream = fopen($uploaded->tempName, 'r+');
			$filename = "{$prefix}_{$uploaded->name}";
			$fullpath = "{$this->webroot}{$this->targetDir}/{$filename}";
			$result = $this->fileSystem->writeStream($fullpath, $stream);
			fclose($stream);

			if ($result) {
				$result = $fileModel->saveFiles($uploaded, $filename, $this->targetDir, $this->type);
				$retval = $retval && $result;
			} else {
				$retval = false;
			}
		}
		return $retval;
	}
}