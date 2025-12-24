<?php
/**
 * Controller Module DC Gradient Hero
 *
 * @version 1.1
 * * @author Design Cart <info@designcart.pl>
 */

namespace Opencart\Catalog\Controller\Extension\DcGradientHero\Module;

class DcGradientHero extends \Opencart\System\Engine\Controller {

    public $cache_directory   = 'extension/dc_gradient_hero/cache/';
    public $cache_uri_path    = 'extension/dc_gradient_hero/cache/';
    public $minified_css      = 'dc-gradient-hero.min.css';
    public $minified_js       = 'dc-gradient-hero.min.js';
    public $minified_css_path = '';
    public $minified_js_path  = '';
    
    public function index(array $setting): string {

        $this->minified_css_path = $this->cache_uri_path.$this->minified_css;
        $this->minified_js_path  = $this->cache_uri_path.$this->minified_js;

        $view = '';
        $language_id = (int)$this->config->get('config_language_id');

        $this->load->language('extension/dc_gradient_hero/theme/dc_minimal');

        // Sprawdzamy, czy ustawienia modułu istnieją dla bieżącego języka
        if (!empty($setting)) {
            
            $this->load->language('extension/dc_gradient_hero/module/dc_gradient_hero');
            
            // Ładujemy niezbędne modele
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            
            // NOWE: Ładujemy nasz własny model do obsługi filtrowania:
            $this->load->model('extension/dc_gradient_hero/module/dc_gradient_hero');
            $this->load->model('extension/dc_gradient_hero/module/dc_css_minify'); 
            $this->load->model('extension/dc_gradient_hero/module/dc_js_minify');   

            $language_code = $this->config->get('config_language');

            $language_id = (int)$this->config->get('config_language_id');
            $data['mod_title']       = html_entity_decode($setting['module_description'][$language_id]['title'], ENT_QUOTES, 'UTF-8') ?? '';
            $data['mod_description'] = isset($setting['module_description'][$language_id]['description']) ? html_entity_decode($setting['module_description'][$language_id]['description'], ENT_QUOTES, 'UTF-8') : '';
            $data['mod_url']         = $setting['module_description'][$language_id]['url'] ?? '';
            $data['mod_anchor']      = html_entity_decode($setting['module_description'][$language_id]['anchor'], ENT_QUOTES, 'UTF-8') ?? '';

            if(!empty($setting['image_width'])){
                $image_width = (int)$setting['image_width'];
            }else{
                $image_width = 1920;
            }

            if(!empty($setting['image_height'])){
                $image_height = (int)$setting['image_height'];
            }else{
                $image_height = 400;
            }

            if(!empty($setting['image']) AND is_file(DIR_IMAGE . $setting['image'])) {
                $data['image'] = $this->model_tool_image->resize( $setting['image'], $image_width, $image_height);
            }

            $data['attr_ID'] = $setting['attr_ID'];

            $module_id = isset($setting['module_id']) ? (int)$setting['module_id'] : 0;
            $data['module_id'] = $module_id;

            $data['button_bg']               = $setting['button_bg']               ?? '#ffffff';
            $data['button_bg_hover']         = $setting['button_bg_hover']         ?? '#eeeeee';
            $data['button_text_color']       = $setting['button_text_color']       ?? '#000000';
            $data['button_text_color_hover'] = $setting['button_text_color_hover'] ?? '#000000';

            $data['heading_color']           = $setting['heading_color']           ?? '#ffffff';
            $data['subheading_color']        = $setting['subheading_color']        ?? '#cccccc';

            $data['gradients']               = $setting['gradients'];
            
            $module_css_files = array(
                'extension/dc_gradient_hero/catalog/view/assets/css/dc_gradient_hero.css'
            );

            $module_js_files = array(
                'extension/dc_gradient_hero/catalog/view/assets/js/granim.js',
                'extension/dc_gradient_hero/catalog/view/assets/js/dc_gradient_hero.js'
            );
            
            require DIR_OPENCART.'/extension/dc_gradient_hero/includes/minifies.php';
            
            $view = $this->load->view('extension/dc_gradient_hero/module/dc_gradient_hero', $data);
        }

        return $view;
    }
}