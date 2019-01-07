<?php

namespace pkpudev\components\web;

/**
 * Usable functions for WebUser
 */
class UserBehavior extends \yii\base\Behavior
{
	/**
	 * @param array $arrRole Roles to be checked
	 * @param bool $default Default return value
	 * @return bool
	 */
	public function canBetween(array $arrRole, $default=false)
	{
		foreach ($arrRole as $roleStr) {
			$default = $default || $this->owner->can($roleStr);
		}

		return $default;
	}
}