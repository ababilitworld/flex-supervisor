<?php
namespace Ababilithub\FlexSupervisor\Package\Plugin\Audit\V1\Concrete\Term;

use Ababilithub\{
    FlexSupervisor\Package\Plugin\Audit\V1\Base\Audit as BaseAudit
};

use const Ababilithub\{
    FlexSupervisor\PLUGIN_NAME,
    FlexSupervisor\PLUGIN_DIR,
    FlexSupervisor\PLUGIN_URL,
    FlexSupervisor\PLUGIN_FILE,
    FlexSupervisor\PLUGIN_PRE_UNDS,
    FlexSupervisor\PLUGIN_PRE_HYPH,
    FlexSupervisor\PLUGIN_VERSION
};

class Audit extends BaseAudit
{
    public function init(array $data = null): static
    {
        $this->object_type = 'term';
        $this->object_id = absint($_GET['object_id'] ?? 0);
        $this->action_id = sanitize_text_field($_GET['action_id'] ?? '');

        $this->init_service();
        $this->init_hook();

        return $this;
    }

    public function init_service()
    {
        // Initialize any services here
    }

    public function init_hook()
    {
        //add_filter(PLUGIN_PRE_UNDS.'_admin_menu', [$this, 'add_menu_items']);
    }

    /**
     * Add default menu items (can be overridden by other plugins/themes)
     */
    public function add_menu_items($menu_items = [])
    {
        $menu_items[] = [
            'type' => 'submenu',
            'parent_slug' => 'flex-supervisor',
            'page_title' => 'Term',
            'menu_title' => 'Term',
            'capability' => 'manage_options',
            'menu_slug' => 'flex-supervisor-audit-terms',
            'callback' => [$this, 'render'],
            'position' => 2,
        ];

        return $menu_items;
    }

    public function applyAuditAction(): void 
    {
        if ($this->action_id == 'view_details') {
            $this->view_details();
        }

        if ($this->action_id == 'view_meta') {
            $this->view_all_meta();
        }
    }

    public function view_details(): void 
    {
        // Validate and get term object
        $term = get_term($this->object_id);
        if (!$term || is_wp_error($term)) {
            $this->render_notice('Term not found.', 'error');
            return;
        }

        // Get all term meta
        $all_meta = get_term_meta($this->object_id);
        
        // Begin output
        $this->render_header($term);
        
        if (empty($all_meta)) {
            $this->render_notice('No meta data found for this term.');
            return;
        }

        $this->render_meta_table($all_meta);
    }

    /**
     * Render the page header with back link
     */
    protected function render_header(\WP_Term $term): void
    {
        $edit_link = get_edit_term_link($term->term_id, $term->taxonomy);
        ?>
        <div class="wrap">
            <h1><?php printf(esc_html__('Term Meta Data for #%d: %s', 'flex-supervisor'), 
                $term->term_id, 
                esc_html($term->name)); ?>
            </h1>
            <p>
                <?php if ($edit_link): ?>
                    <a href="<?php echo esc_url($edit_link); ?>" class="button">
                        &larr; <?php esc_html_e('Back to term editor', 'flex-supervisor'); ?>
                    </a>
                <?php endif; ?>
            </p>
        <?php
    }

    /**
     * Render meta data in a table
     */
    protected function render_meta_table(array $all_meta): void
    {
        ?>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th width="25%"><?php esc_html_e('Meta Key', 'flex-supervisor'); ?></th>
                    <th width="75%"><?php esc_html_e('Meta Value', 'flex-supervisor'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_meta as $meta_key => $meta_values): ?>
                    <tr>
                        <td><strong><?php echo esc_html($meta_key); ?></strong></td>
                        <td>
                            <?php foreach ($meta_values as $value): ?>
                                <?php $this->render_meta_value($value); ?>
                                <hr class="meta-value-separator">
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $this->render_styles();
    }

    public function view_all_meta(): void
    {
        // Validate and get term object
        $term = get_term($this->object_id);
        if (!$term || is_wp_error($term)) {
            $this->render_notice('Term not found.', 'error');
            return;
        }

        // Get all term meta
        $all_meta = get_term_meta($this->object_id);
        
        // Begin output
        $this->render_header($term);
        
        if (empty($all_meta)) {
            $this->render_notice('No meta data found for this term.');
            return;
        }

        $this->render_meta_table($all_meta);
    }

    // The following methods remain the same as in your Post Audit class
    // since they're generic enough to work with both posts and terms
    
    /**
     * Render individual meta value
     */
    protected function render_meta_value($value): void
    {
        $unserialized = maybe_unserialize($value);
        
        if (is_array($unserialized) || is_object($unserialized)) {
            echo '<pre>'. esc_html(print_r($unserialized, true)) .'</pre>';
        } else {
            echo esc_html($value);
        }
    }

    /**
     * Render admin notice
     */
    protected function render_notice(string $message, string $type = 'info'): void
    {
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?>">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }

    /**
     * Add custom styles
     */
    protected function render_styles(): void
    {
        ?>
        <style>
            .wrap { 
                margin: 20px; 
            }
            table.widefat { 
                border-collapse: collapse; 
                margin-top: 20px; 
            }
            table.widefat th, 
            table.widefat td { 
                padding: 10px; 
                border: 1px solid #e5e5e5; 
                vertical-align: top; 
            }
            table.widefat th { 
                background-color: #f7f7f7; 
                font-weight: 600; 
            }
            table.widefat pre { 
                margin: 0; 
                white-space: pre-wrap; 
                max-width: 100%; 
                overflow-x: auto;
                background: #f5f5f5;
                padding: 5px;
                border-radius: 3px;
            }
            .notice { 
                margin: 20px 0 !important; 
            }
            hr.meta-value-separator {
                margin: 5px 0; 
                border: 0; 
                border-top: 1px dashed #ccc;
            }
        </style>
        <?php
    }
}