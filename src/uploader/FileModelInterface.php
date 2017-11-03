<?php

namespace pkpudev\components\uploader;

use yii\db\ActiveRecordInterface;
use yii\web\UploadedFile;
use yii\web\User;

interface FileModelInterface
{
	/**
	 * Construct
	 * @param ActiveRecordInterface $model
	 * @param user $user
	 */
	public function __construct(ActiveRecordInterface $model, User $user);
	/**
	 * Saving file to a model and File model
	 * @param UploadedFile $uploaded
	 * @param string $filename
	 * @param string $targetDir
	 * @param string $desc
	 */
	public function saveFile(UploadedFile $uploaded, $filename, $targetDir, $desc=null);
}