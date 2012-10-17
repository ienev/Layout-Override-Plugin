<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Override Joomla Views and Layouts
 *
 * @package     Plugin
 * @subpackage  System
 * @since       2.5
 */
class plgSystemLayout extends JPlugin
{
    function onAfterInitialise()
    {
        /** Set criteria for the View you want to override */
        if ((int)$this->params->get('override_front_archive', 1) && JFactory::getApplication()->getName() == 'site'
            && JFactory::getApplication()->input->get('option', '', 'cmd') == 'com_content'
            && JFactory::getApplication()->input->get('view', '', 'cmd') == 'archive'
            && JFactory::getApplication()->input->get('layout', 'default', 'cmd') == 'default'
        ) {
			
            /** Override JRequest View and Layout - register the view (example in the layout/com_content folder) */
            JRequest::setVar('view', 'archive2');
            JRequest::setVar('layout', 'default2');
            JLoader::register('ContentViewArchive2', __DIR__ . '/com_content/views/archive2/ContentViewArchive2.php');
        }
		
        /** Set criteria for the View you want to override */
        if ((int)$this->params->get('override_admin_edit', 1) && JFactory::getApplication()->getName() == 'administrator'
            && JFactory::getApplication()->input->get('option', '', 'cmd') == 'com_content'
            && JFactory::getApplication()->input->get('view', '', 'cmd') == 'article'
            && JFactory::getApplication()->input->get('layout', '', 'cmd') == 'edit'
        ) {
			
            /** Override JRequest View and Layout - register the view (example in the layout/com_content folder) */
            JRequest::setVar('view', 'article2');
            JRequest::setVar('layout', 'edit2');
            JLoader::register('ContentViewArticle2', __DIR__ . '/com_content/views/article/ContentViewArticle2.php');
        }
    }
}
