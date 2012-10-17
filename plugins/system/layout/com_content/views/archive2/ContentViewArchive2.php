<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/** Override this View */
JLoader::register('ContentViewArchive', JPATH_BASE . '/components/com_content/views/archive/view.html.php');

/** Override this Model */
JLoader::register('ContentModelArchive', JPATH_BASE . '/components/com_content/models/archive.php');

/**
 * Extend the real View
 */
class ContentViewArchive2 extends ContentViewArchive
{
	public function display($tpl = null)
	{
        /** Put your view into this folder - or change the path */
        $this->addTemplatePath(__DIR__ . '/tmpl/');

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_layout', JPATH_ADMINISTRATOR);
		
		$app = JFactory::getApplication();
		$user		= JFactory::getUser();

		$state 		= $this->get('State');
		$items 		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();

		// Get the page/component configuration
		$params = &$state->params;

		// Compute the article slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = &$items[$i];
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// No link for ROOT category
			if ($item->parent_alias == 'root') {
				$item->parent_slug = null;
			} else {
				$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
			}
			$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			$item->event = new stdClass;

			$dispatcher = JEventDispatcher::getInstance();

			$item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'com_content.archive');

			$results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$item->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$item->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$item->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		$form = new stdClass;
		
		$this->filter_year = $state->get('filter.year');
		$this->filter_month = str_pad($state->get('filter.month'), 2, '0', STR_PAD_LEFT);
		
		// Month Field
		$months = array(
			'01' => JText::_('JANUARY'),
			'02' => JText::_('FEBRUARY'),
			'03' => JText::_('MARCH'),
			'04' => JText::_('APRIL'),
			'05' => JText::_('MAY'),
			'06' => JText::_('JUNE'),
			'07' => JText::_('JULY'),
			'08' => JText::_('AUGUST'),
			'09' => JText::_('SEPTEMBER'),
			'10' => JText::_('OCTOBER'),
			'11' => JText::_('NOVEMBER'),
			'12' => JText::_('DECEMBER')
		);
		$form->monthField = JHtml::_(
			'select.genericlist',
			$months,
			'month',
			array(
				'list.attr' => 'size="1" class="inputbox"',
				'list.select' => $this->filter_month,
				'option.key' => null
			)
		);
		
		// Year Field
		$years = array();
		for ($i = 2000; $i <= 2020; $i++) {
			$years[] = JHtml::_('select.option', $i, $i);
		}
		$form->yearField = JHtml::_(
			'select.genericlist',
			$years,
			'year',
			array('list.attr' => 'size="1" class="inputbox"', 'list.select' => $this->filter_year)
		);
		
		$form->limitField = $pagination->getLimitBox();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->filter     = $state->get('list.filter');
		$this->form       = &$form;
		$this->months     = &$months;
		$this->items      = &$items;
		$this->params     = &$params;
		$this->user       = &$user;
		$this->pagination = &$pagination;

		$this->_prepareDocument();
		
		// we don't call the direct parent method as it will override our changes
		JViewLegacy::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
/**
 * Extend the real model
 */
class ContentModelArchive2 extends ContentModelArchive {
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();

		$app = JFactory::getApplication();

		// Add archive properties
		$params = $this->state->params;

		// Filter on published and archived articles
		$this->setState('filter.published', array(1,2));
		
		// Filter on category
		$this->setState('filter.category_id', $app->input->getInt('catid'));

		// Filter on month, year
		$this->setState('filter.month', $app->input->getInt('month'));
		$this->setState('filter.year', $app->input->getInt('year'));

		// Optional filter text
		$this->setState('list.filter', $app->input->getString('filter-search'));

		// Get list limit
		$itemid = $app->input->get('Itemid', 0, 'int');
		$limit = $app->getUserStateFromRequest('com_content.archive.list' . $itemid . '.limit', 'limit', $params->get('display_num'), 'uint');
		$this->setState('list.limit', $limit);
	}
}
