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

		$this->webroot = \Yii::getAlias('webroot');
		if ($this->isOneInstance) {
			$file = UploadedFile::getInstance($this->model, $this->attribute);
			$this->files = [$file];
			$this->isValid = $file instanceof UploadedFile;
		} else {
			$this->files = UploadedFile::getInstances($this->model, $this->attribute);
			$this->isValid = is_array($this->files) && ($this->files[0] instanceof UploadedFile);
		}
	}

	/**
	 * @param string $prefix
	 * @param FileModelInterface $fileModel
	 */
	public function upload($prefix, FileModelInterface $fileModel)
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