<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Selection;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Model\SelectionQuery;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Install\Database;
use Thelia\Model\Resource;
use Thelia\Model\ResourceQuery;
use Thelia\Module\BaseModule;

class Selection extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'selection';
    const ROUTER = 'router.selection';

    const RESOURCES_SELECTION = 'admin.selection';
    const CONFIG_ALLOW_PROFILE_ID = 'admin_profile_id';

    /**
     * @param ConnectionInterface|null $con
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function postActivation(ConnectionInterface $con = null): void
    {
        try {
            SelectionQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . '/Config/TheliaMain.sql']);
        }

        $this->addRessource(self::RESOURCES_SELECTION);

        //Initialize the module_config
        self::setConfigValue(self::CONFIG_ALLOW_PROFILE_ID, '');
    }

    /**
     * @param ConnectionInterface|null $con
     * @param false $deleteModuleData
     */
    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false): void
    {
        $database = new Database($con);
        $database->insertSql(null, [__DIR__ . '/Config/destroy.sql']);
    }

    public function update($currentVersion, $newVersion, ConnectionInterface $con = null): void
    {
        $finder = Finder::create()
            ->name('*.sql')
            ->depth(0)
            ->sortByName()
            ->in(__DIR__ . DS . 'Config' . DS . 'update');

        $database = new Database($con);

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            if (version_compare($currentVersion, $file->getBasename('.sql'), '<')) {
                $database->insertSql(null, [$file->getPathname()]);
            }
        }
    }

    /**
     * @param $code
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function addRessource($code)
    {
        if (null === ResourceQuery::create()->findOneByCode($code)) {
            $resource = new Resource();
            $resource->setCode($code);
            $resource->setTitle($code);
            $resource->save();
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR.ucfirst(self::getModuleCode()).'/I18n/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
