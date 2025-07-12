<?php
namespace Ababilithub\FlexSupervisor\Package\Plugin\Menu\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\Menu\V1\Contract\Menu as MenuContract, 
    FlexWordpress\Package\Menu\V1\Factory\Menu as MenuFactory,
    FlexSupervisor\Package\Plugin\Menu\V1\Concrete\Supervisor\Menu as SupervisorMenu,
    FlexSupervisor\Package\Plugin\Menu\V1\Concrete\Audit\Post\Menu as PostAuditMenu,
    FlexSupervisor\Package\Plugin\Menu\V1\Concrete\Audit\Term\Menu as TermAuditMenu, 
};

class  Menu extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        $this->set_items(
                [
                    SupervisorMenu::class,
                    PostAuditMenu::class,
                    TermAuditMenu::class,
            ]
        );
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = MenuFactory::get($item);

            if ($item_instance instanceof MenuContract) 
            {
                $item_instance->register();
            }
        }
    }
}