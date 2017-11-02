<?php

namespace pkpudev\components\uploader;

use yii\db\ActiveRecordInterface;
use yii\web\UploadedFile;
use yii\web\User;

interface FileModelInterface
{
	/**
	 * Construct
	 */
	public function __construct(ActiveRecordInterface $model, User $user);
	/**
	 * Saving file to a model and File model
	 */
	public function saveFile(UploadedFile $uploaded, string $filename, string $targetDir, string $desc=null);
}