<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini
 *
 * @copyright (c) 2010, 2011 Lopo <lopo@losys.eu>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */
namespace App\BackendModule;

use Nette\Utils\Html,
	Lohini\Components\DataGrid;

/**
 * Plugins presenter
 *
 * @author Lopo <lopo@losys.eu>
 */
class PluginsPresenter
extends BasePresenter
{
	/**
	 * @return \Lohini\Components\DataGrid\DataGrid
	 */
	protected function createComponentGridMain()
	{
		$grid=new DataGrid\DataGrid;
		$ds=new \Lohini\Database\DataSources\Doctrine\QueryBuilder(
				$this->context->sqldb->entityManager->createQueryBuilder()
				->select('p')
				->from('LE:Plugin', 'p')
				);
		$ds->setMapping(
				array(
					'id' => 'p.id',
					'name' => 'p.name',
					'state' => 'p.state'
					)
				);
		$grid->setDataSource($ds);
		$grid->keyName='id';

		$grid->addColumn('name')->addTextFilter();
		$grid->addColumn('state')->addSelectboxFilter();

		$ac=$grid->addActionColumn('Actions');
		$grid->addAction('Install', 'pluginInstall!', NULL, TRUE);
		$grid->addAction('Update', 'pluginUpdate!', NULL, TRUE);
		$grid->addAction('Uninstall', 'pluginUninstall!', NULL, TRUE);
		$grid->addAction('Enable', 'pluginEnable!', NULL, TRUE);
		$grid->addAction('Disable', 'pluginDisable!', NULL, TRUE);
		$grid->addAction('Remove', 'pluginRemove!', NULL, TRUE);

		$rdr=$grid->getRenderer();
		$rdr->onActionRender[]=callback($this, 'actionRenderGridMain');

		return $grid;
	}

	public function actionRenderGridMain(Html $action, $data)
	{
		$ai=Html::el('span')->class('ui-icon')->style('display: block; float: left;');
		$plugin=$this->context->sqldb->getRepository('LE:Plugin')->findOneById($data['id']);
		switch ($action->title) {
			case 'Install':
				$ai->addClass('ui-icon-circle-plus');
				if ($plugin->state!==\Lohini\Plugins\Plugin::STATE_REGISTERED) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			case 'Update':
				$ai->addClass('ui-icon-circle-triangle-n');
				$class=$plugin->pluginClass;
				if (!$plugin->installed || $plugin->iversion==$class::VERSION) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			case 'Uninstall':
				$ai->addClass('ui-icon-circle-minus');
				if (!$plugin->installed) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			case 'Enable':
				$ai->addClass('ui-icon-circle-check');
				$class=$plugin->pluginClass;
				if (!$plugin->installed || $plugin->enabled || $plugin->iversion!=$class::VERSION) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			case 'Disable':
				$ai->addClass('ui-icon-circle-close');
				if (!$plugin->enabled) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			case 'Remove':
				$ai->addClass('ui-icon-trash');
				if ($plugin->installed) {
					$ai->addClass('ui-state-disabled');
					$action->setName('span');
					}
				else {
					$action->setName('a');
					}
				break;
			}
		$action->setHtml($ai);
		return $action;
	}

	/**
	 * @param int $id
	 */
	public function handlePluginInstall($id)
	{
		$sqldb=$this->context->sqldb;
		try {
			$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
			if ($entity->installed) {
				return;
				}
			if (($ret=$sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->installPlugin($entity->name))!==TRUE) {
				$this->flashMessage('Install error: '.$ret->getMessage(), 'error');
				return;
				}
			}
		catch (\Exception $e) {
			$this->flashMessage('Install error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage('Install successfull', 'success');
		$this['gridMain']->invalidateControl();
		
	}

	/**
	 * @param int $id
	 */
	public function handlePluginUpdate($id)
	{
		$sqldb=$this->context->sqldb;
		try {
			$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
			if ($entity->enabled) {
				return;
				}
			if ($sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->updatePlugin($entity->name)!==TRUE) {
				$this->flashMessage('Update error: '.$e->getMessage(), 'error');
				return;
				}
			}
		catch (\Exception $e) {
			$this->flashMessage('Update error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage('Update successfull', 'success');
		$this['gridMain']->invalidateControl();
		
	}

	/**
	 * @param int $id
	 */
	public function handlePluginUninstall($id)
	{
		$sqldb=$this->context->sqldb;
		$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
		if ($entity===NULL || !$entity->installed) {
			return;
			}
		if ($sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->uninstallPlugin($entity->name)!==TRUE) {
			$this->flashMessage('Uninstall error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage('Uninstall successfull', 'success');
		$this['gridMain']->invalidateControl();
	}

	/**
	 * @param int $id
	 */
	public function handlePluginEnable($id)
	{
		$sqldb=$this->context->sqldb;
		$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
		if ($entity===NULL || !$entity->installed || $entity->enabled) {
			return;
			}
		if ($sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->enablePlugin($entity->name)!==TRUE) {
			$this->flashMessage('Enabling error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage("Plugin '$entity->name' enabled", 'success');
		$this['gridMain']->invalidateControl();
	}

	/**
	 * @param int $id
	 */
	public function handlePluginDisable($id)
	{
		$sqldb=$this->context->sqldb;
		$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
		if ($entity===NULL || !$entity->enabled) {
			return;
			}
		if ($sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->disablePlugin($entity->name)!==TRUE) {
			$this->flashMessage('Disabling error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage("Plugin '$entity->name' disabled", 'success');
		$this['gridMain']->invalidateControl();
	}

	/**
	 * @param int $id
	 */
	public function handlePluginRemove($id)
	{
		$sqldb=$this->context->sqldb;
		$entity=$sqldb->getRepository('LE:Plugin')->findOneById($id);
		if ($entity===NULL || $entity->installed) {
			return;
			}
		if ($sqldb->getModelService('Lohini\Database\Models\Entities\Plugin')->removePlugin($entity->name)!==TRUE) {
			$this->flashMessage('Removing error: '.$e->getMessage(), 'error');
			return;
			}
		$this->flashMessage("Plugin '$entity->name' removed", 'success');
		$this['gridMain']->invalidateControl();
	}
}
