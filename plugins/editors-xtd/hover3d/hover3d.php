<?php

defined('_JEXEC') or die;

/**
 * Provides button to insert an image with hover3d into content edit box
 */
class plgButtonHover3d extends JPlugin
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
				function insertImageHover3d(editor) {
					var content = '<div class=\"hover3d\">\\n';
					content += '  <a class=\"hover3d-link\" href=\"#\" >\\n';
					content += '  <div class=\"hover3d-card\">\\n';
					content += '    <div class=\"hover3d-image\"><img src=\"images/sampledata/fruitshop/apple.jpg\" alt=\"\" /></div>\\n';
					content += '    <div class=\"hover3d-detail\">\\n';
					content += '      <div>\\n';
					content += '        <h3 class=\"hover3d-title\">Your title</h3>\\n';
					content += '        <div class=\"hover3d-text\">Your text</div>\\n';
					content += '      </div>\\n';
					content += '    </div>\\n';
					content += '  </div>\\n';
					content += '  </a>\\n';
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
			$button->set('text', 'hover3D Image');
			$button->name    = 'pictures';
			
			$button->set('onclick', 'insertImageHover3d(\'' . $name . '\');return false;');

			return $button;
		}
		
		return false;
	}
}