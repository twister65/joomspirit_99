<?php

defined('_JEXEC') or die;

/**
 * Provides button to insert image-with-text into content edit box
 */
class plgButtonImageText extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  Joomla!3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Display the button.
	 *
	 * @param   string   $name    The name of the button to display.
	 * @param   string   $asset   The name of the asset being edited.
	 * @param   integer  $author  The id of the author owning the asset being edited.
	 *
	 * @return  JObject  The button options as JObject or false if not allowed
	 *
	 * @since   1.5
	 */
	public function onDisplay($name, $asset, $author)
	{
		$app       = JFactory::getApplication();
		$user      = JFactory::getUser();
		$extension = $app->input->get('option');

		// For categories we check the extension (ex: component.section)
		if ($extension === 'com_categories')
		{
			$parts     = explode('.', $app->input->get('extension', 'com_content'));
			$extension = $parts[0];
		}

		$asset = $asset !== '' ? $asset : $extension;

		if ($user->authorise('core.edit', $asset)
			|| $user->authorise('core.create', $asset)
			|| (count($user->getAuthorisedCategories($asset, 'core.create')) > 0)
			|| ($user->authorise('core.edit.own', $asset) && $author === $user->id)
			|| (count($user->getAuthorisedCategories($extension, 'core.edit')) > 0)
			|| (count($user->getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author === $user->id))
		{
			$js = "
				function insertImageText(editor) {
					var content = '<div class=\"image-text-right color-lightslategrey\">\\n';
					content += '  <a class=\"image-text-image\" style=\"background-image:url(\'';
					content += 'images/sampledata/fruitshop/apple.jpg';
					content += '\');\"  href=\"#\" ></a>\\n';
					content += '  <div class=\"image-text-column\">\\n';
					content += '    <div>\\n';
					content += '      <h2>Your Title</h2>\\n';
					content += '      <p>Your text here</p>\\n';
					content += '      <p><a  href=\"#\" >View more</a></p>\\n';
					content += '    </div>\\n';
					content += '  </div>\\n';
					content += '</div>\\n';
					jInsertEditorText(content, editor);
				}
				";

			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);

			$button = new JObject;
			$button->modal   = false;
			$button->class   = 'btn';
			$button->link    = '#';
			$button->set('text', 'Image with text');
			$button->name    = 'pictures';

			$button->set('onclick', 'insertImageText(\'' . $name . '\');return false;');

			return $button;
		}
		
		return false;
	}
}