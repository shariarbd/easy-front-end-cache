<?php
if ( ! defined( 'ABSPATH' ) ) exit;

trait EFEC_Admin_Renderers {

    public static function render_checkbox($args) {
        $option      = $args['option'];
        $value       = get_option($option);
        $description = $args['description'] ?? '';
        ?>
        <label class="efc-toggle">
            <input type="checkbox" name="<?php echo esc_attr($option); ?>" value="1" <?php checked(1, $value); ?> />
            <span class="efc-slider"></span>
        </label>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_number($args) {
        $option      = $args['option'];
        $default     = $args['default'] ?? '';
        $value       = get_option($option, $default);
        $description = $args['description'] ?? '';
        ?>
        <input type="number" class="small-text"
               name="<?php echo esc_attr($option); ?>"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($default); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_text($args) {
        $option      = $args['option'];
        $default     = $args['default'] ?? '';
        $value       = get_option($option, $default);
        $description = $args['description'] ?? '';
        ?>
        <input type="text" class="regular-text"
               name="<?php echo esc_attr($option); ?>"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($default); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_textarea($args) {
        $option      = $args['option'];
        $value       = get_option($option, '');
        $description = $args['description'] ?? '';
        ?>
        <textarea name="<?php echo esc_attr($option); ?>" rows="5" cols="50" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_select($args) {
        $option      = $args['option'];
        $choices     = $args['choices'];
        $value       = get_option($option, '');
        $description = $args['description'] ?? '';
        ?>
        <select name="<?php echo esc_attr($option); ?>" class="regular-select">
            <?php foreach ($choices as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_page_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $pages    = get_pages(['sort_column'=>'post_title','sort_order'=>'ASC']);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($pages as $page) {
            $sel = in_array($page->ID,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($page->ID).'" '.$sel.'>'.esc_html($page->post_title).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_post_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $posts    = get_posts(['numberposts'=>-1,'post_type'=>'post']);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($posts as $post) {
            $sel = in_array($post->ID,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($post->ID).'" '.$sel.'>'.esc_html($post->post_title).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_category_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $cats     = get_categories(['hide_empty'=>false]);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($cats as $cat) {
            $sel = in_array($cat->term_id,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($cat->term_id).'" '.$sel.'>'.esc_html($cat->name).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_cpt_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $cpts     = get_post_types(['public'=>true,'_builtin'=>false],'objects');
        if (empty($cpts)) {
            echo '<p>'.esc_html__('No custom post types found.','easy-front-end-cache').'</p>';
            return;
        }
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($cpts as $cpt) {
            $sel = in_array($cpt->name,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($cpt->name).'" '.$sel.'>'.esc_html($cpt->labels->singular_name).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }
}