<?php
namespace Ababilithub\FlexSupervisor\Package\Plugin\Menu\V1\Concrete\Audit\Post;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexPhp\Package\Mixin\V1\Standard\Mixin as StandardMixin,
    FlexWordpress\Package\Menu\V1\Base\Menu as BaseMenu,
    FlexSupervisor\Package\Plugin\Audit\V1\Concrete\Post\Audit as PostAudit,
};

use const Ababilithub\{
    FlexSupervisor\PLUGIN_PRE_UNDS,
    FlexSupervisor\PLUGIN_DIR,
};

if (!class_exists(__NAMESPACE__.'\Menu')) 
{

    class Menu extends BaseMenu
    {
        public $postAudit;

        public function init(array $data = []) : static
        {
            $this->menu_filter_name = PLUGIN_PRE_UNDS.'_admin_menu';
            $this->postAudit = new PostAudit();
            $this->init_service();
            $this->init_hook();
            return $this;
        }

        public function init_service() : void
        {
            
        }

        public function init_hook() : void
        {
            // Add filter to collect menu items
            add_filter($this->menu_filter_name, [$this, 'add_menu_items']);
            
        }

        /**
         * Add default menu items
         */
        public function add_menu_items($menu_items = [])
        {

            $menu_items[] = [
                'type' => 'submenu',
                'parent_slug' => 'flex-supervisor',
                'page_title' => 'Post',
                'menu_title' => 'Post',
                'capability' => 'manage_options',
                'menu_slug' => 'flex-supervisor-audit-post',
                'callback' => [$this->postAudit, 'render'],
                'position' => 2,
            ];

            return $menu_items;
        }

        /**
         * Custom main page render
         */
        public function render_main_page()
        {
            echo '<div class="wrap">';
            echo '<h1>Main Menu Dashboard</h1>';
            echo '<p>Welcome to Flex Bangla Land administration panel.</p>';
            echo '</div>';
        }

        /**
         * Custom main page render
         */
        public function render_submenu()
        {
            echo '<div class="wrap">';
            echo '<h1>Sub Menu Dashboard</h1>';
            echo '<p>Welcome to Flex Bangla Land administration panel.</p>';
            echo '</div>';
        }
        
    }
}
