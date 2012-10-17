<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');
$heading = (is_array($this->items) && isset($this->items[0]) && $this->items[0]->category_title) ? $this->items[0]->category_title : $this->params->get('page_heading');

?>
<div class="archive<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<div class="page-header">
<h1>
	<?php echo JText::sprintf('PLG_SYSTEM_LAYOUT_HEADING', $this->escape($heading), $this->months[$this->filter_month], $this->filter_year); ?>
</h1>
</div>
<?php endif; ?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_content&view=archive')?>" method="post" class="form-inline">
	<fieldset class="filters">
	<div class="filter-search alert alert-info">
		<?php if ($this->params->get('filter_field') != 'hide') : ?>
		<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
		<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox span2" onchange="document.getElementById('adminForm').submit();" />
		<?php endif; ?>

		<?php echo $this->form->monthField; ?>
		<?php echo $this->form->yearField; ?>
		<?php echo $this->form->limitField; ?>

	<button type="submit" class="btn btn-primary"><?php echo JText::_('JGLOBAL_FILTER_BUTTON'); ?></button>
	</div>
	<input type="hidden" name="view" value="archive" />
	<input type="hidden" name="option" value="com_content" />
	<input type="hidden" name="limitstart" value="0" />
	</fieldset>

	<?php echo $this->loadTemplate('items'); ?>
</form>
</div>
