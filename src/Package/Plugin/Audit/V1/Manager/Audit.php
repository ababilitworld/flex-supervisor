<?php
namespace Ababilithub\FlexSupervisor\Package\Plugin\Audit\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexSupervisor\Package\Plugin\Audit\V1\Factory\Audit as AuditFactory,
    FlexSupervisor\Package\Plugin\Audit\V1\Contract\Audit as AuditContract, 
    FlexSupervisor\Package\Plugin\Audit\V1\Concrete\Post\Audit as PostAudit,
    FlexSupervisor\Package\Plugin\Audit\V1\Concrete\Term\Audit as TermAudit,
       
};

class Audit extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    public function init(): void
    {
        $this->set_items([
            PostAudit::class,
            TermAudit::class,
            // Add more posttype classes here...
        ]);
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $itemClass) 
        {
            $posttype = AuditFactory::get($itemClass);

            if ($posttype instanceof AuditContract) 
            {
                $posttype->register();
            }
        }
    }
}
