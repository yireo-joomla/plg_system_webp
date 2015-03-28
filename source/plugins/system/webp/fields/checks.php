<?php
/**
 * Joomla! System plugin - WebP
 *
 * @author    Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link      http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Form Field-class for providing some environment checks for WebP
 */
class YireoFormFieldChecks extends JFormField
{
	/*
	 * Form field type
	 */
	public $type = 'Checks';

	/*
	 * HTML output
	 */
	protected $html = array();

	/*
	 * Method to construct the HTML of this element
	 *
	 * @param null
	 * @return string
	 */
	protected function getInput()
	{
		if (function_exists('imagewebp'))
		{
			$this->addNotice(JText::_('PLG_SYSTEM_WEBP_FIELD_CHECKS_GD_SUPPORT_TRUE'));
		}
		else
		{
			$this->addError(JText::_('PLG_SYSTEM_WEBP_FIELD_CHECKS_GD_SUPPORT_FALSE'));
		}

		if (function_exists('exec'))
		{
			$this->addNotice(JText::_('PLG_SYSTEM_WEBP_FIELD_CHECKS_EXEC_TRUE'));
		}
		else
		{
			$this->addError(JText::_('PLG_SYSTEM_WEBP_FIELD_CHECKS_EXEC_FALSE'));
		}

		return implode('', $this->html);
	}

	/*
	 * Method to add a notice to the HTML output
	 *
	 *  @param string $message
	 */
	protected function addNotice($message)
	{
		$this->html[] = '<p><span class="label label-success">' . $message . '</span></p>';
	}

	/*
	 * Method to add an error to the HTML output
	 *
	 * @param string $message
	 */
	protected function addError($message)
	{
		$this->html[] = '<p><span class="label label-warning">' . $message . '</span></p>';
	}
}
