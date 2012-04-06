<?php

/**
 * Class containing static methods to install/upgrade/uninstall the addon;
 * and hook into code events to extend the default XenForo functionality.
 *
 * @author Shadab Ansari
 * @package GeekPoint_ConvPreview
 */
class GeekPoint_ConvPreview_Addon
{
	/**
	 * Hook into the "load_class_controller" code event
	 * to dynamically extend a controller class.
	 *
	 * @param string $class
	 * @param array $extend
	 */
	public static function loadClassController($class, array &$extend)
	{
		if ($class === 'XenForo_ControllerPublic_Conversation')
		{
			$extend[] = 'GeekPoint_ConvPreview_ControllerPublic_Conversation';
		}
	}

	/**
	 * Hook into the "template_post_render" code event
	 * to dynamically manipulate a rendered template.
	 *
	 * @param string $templateName
	 * @param string $output
	 * @param array $containerData
	 * @param XenForo_Template_Abstract $template
	 */
	public static function templatePostRender($templateName, &$output, array &$containerData, XenForo_Template_Abstract $template)
	{
		if ($templateName === 'conversation_list')
		{
			/*
			 * Match all conversation links on this page
			 * and invoke a callback method to make changes.
			 */

			$pattern = '(<h3 class="title">\s*<a href="([^"]+)")(>.+?</a>.+?</h3>)';
			$callback = array(__CLASS__, '_manipulateConversationLink');

			$output = preg_replace_callback('#' . $pattern . '#s', $callback, $output);

			/*
			 * The "preview_tooltip" template needs to be included
			 * to enable those sexy ajax tooltips.
			 */

			$output .= $template->create('preview_tooltip')->render();
		}
	}

	/**
	 * Callback method for manipulating conversation links to enable PreviewTooltips.
	 * Contents of the array:
	 * 		[0] => The complete matched expression
	 * 		[1] => Text before ">" of the start tag for first link
	 * 		[2] => Conversation URL
	 * 		[3] => Text including and after ">"
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	protected static function _manipulateConversationLink(array $matches)
	{
		$previewUrl = $matches[2];

		/*
		 * Can't generate link using the conventional XenForo_Link class, at this point.
		 * So we've to resort to such hackish methods.
		 */

		if (strrpos($previewUrl, '/unread') !== false)
		{
			$previewUrl = str_replace('/unread', '/preview-participants', $previewUrl);
		}
		else
		{
			$previewUrl .= 'preview-participants';
		}

		return $matches[1] . ' title="" class="PreviewTooltip" data-previewUrl="' . $previewUrl . '"' . $matches[3];
	}
}