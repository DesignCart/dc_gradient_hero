<?php
/**
 * Controller Module DC Gradient Hero
 *
 * @version 1.0
 * 
 * @author Design Cart <info@designcart.pl>
 */

namespace Opencart\Admin\Controller\Extension\DcGradientHero\Module;
class DcGradientHero extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index(): void {
        $x = version_compare(VERSION, '4.0.2.0', '>=') ? '.' : '|';

        $this->load->language('extension/dc_gradient_hero/module/dc_gradient_hero');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript(HTTP_SERVER . 'view/javascript/ckeditor/ckeditor.js');
        $this->document->addScript(HTTP_SERVER . 'view/javascript/ckeditor/adapters/jquery.js');

        $this->load->model('setting/module');
        $this->load->model('setting/extension');
        $this->load->model('catalog/category');
        $this->load->model('catalog/option');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('dc_gradient_hero.dc_gradient_hero', $this->request->post);
                $module_id = $this->db->getLastId();

                $module_settings = $this->model_setting_module->getModule($module_id);
                $module_settings['module_id'] = $module_id;

                $this->model_setting_module->editModule($module_id, $module_settings);
            } else {
                $post = $this->request->post;
                $post['module_id'] = $this->request->get['module_id'];
                $this->model_setting_module->editModule($this->request->get['module_id'], $post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['form'])) {
            $data['error_form'] = $this->error['form'];
        } else {
            $data['error_form'] = array();
        }

        $url = '';

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/dc_gradient_hero/module/dc_gradient_hero', 'user_token=' . $this->session->data['user_token'] . $url)
        );

        $data['action'] = $this->url->link('extension/dc_gradient_hero/module/dc_gradient_hero' . $x . 'save', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

        if (isset($this->request->get['module_id'])) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['attr_ID'])) {
            $data['attr_ID'] = $this->request->post['attr_ID'];
        } elseif (!empty($module_info)) {
            $data['attr_ID'] = $module_info['attr_ID'];
        } else {
            $data['attr_ID'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->post['image_width'])) {
            $data['image_width'] = $this->request->post['image_width'];
        } elseif (!empty($module_info['image_width'])) {
            $data['image_width'] = $module_info['image_width'];
        } else {
            $data['image_width'] = 1920;
        }

        if (isset($this->request->post['image_height'])) {
            $data['image_height'] = $this->request->post['image_height'];
        } elseif (!empty($module_info['image_height'])) {
            $data['image_height'] = $module_info['image_height'];
        } else {
            $data['image_height'] = 1280;
        }

        if (isset($this->request->post['module_description'])) {
            $data['module_description'] = $this->request->post['module_description'];
        } elseif (!empty($module_info)) {
            $data['module_description'] = $module_info['module_description'];
        } else {
            $data['module_description'] = array();
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($module_info['image'])) {
            $data['image'] = $module_info['image'];
        } else {
            $data['image'] = '';
        }

        if (isset($this->request->post['gradients'])) {
            $data['gradients'] = $this->request->post['gradients'];
        } elseif (!empty($module_info['gradients'])) {
            $data['gradients'] = $module_info['gradients'];
        } else {
            $data['gradients'] = [];
        }

        $this->load->model('tool/image');

        if (!empty($data['image']) && is_file(DIR_IMAGE . $data['image'])) {
            $data['image_thumb'] = $this->model_tool_image->resize($data['image'], 100, 100);
        } else {
            $data['image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $color_fields = [
            'button_bg'                 => '#ffffff',
            'button_bg_hover'           => '#eeeeee',
            'button_text_color'         => '#000000',
            'button_text_color_hover'   => '#000000',
            'heading_color'             => '#ffffff',
            'subheading_color'          => '#cccccc'
        ];

        foreach ($color_fields as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (!empty($module_info) && isset($module_info[$key])) {
                $data[$key] = $module_info[$key];
            } else {
                $data[$key] = $default;
            }
        }

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['tab_general']     = $this->load->view('extension/dc_gradient_hero/module/elements/general', $data);
        $data['tab_gradients']   = $this->load->view('extension/dc_gradient_hero/module/elements/gradients', $data);
        $data['tab_settings']    = $this->load->view('extension/dc_gradient_hero/module/elements/settings', $data);
        $data['header']          = $this->load->controller('common/header');
        $data['column_left']     = $this->load->controller('common/column_left');
        $data['footer']          = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/dc_gradient_hero/module/dc_gradient_hero', $data));
    }

    /**
     * Save Module Data.
     * 
     * @return void
     */
	public function save(): void {
		$this->load->language('extension/dc_gradient_hero/module/dc_gradient_hero');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/dc_gradient_hero/module/dc_gradient_hero')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

        if ((mb_strlen($this->request->post['name']) < 3) || (mb_strlen($this->request->post['name']) > 64)) {
            $json['error']['name'] = $this->language->get('error_name');
        }

		if (!$json) {
			$this->load->model('setting/module');

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('dc_gradient_hero.dc_gradient_hero', $this->request->post);
                $module_id = $this->db->getLastId();

                $module_settings = $this->model_setting_module->getModule($module_id);
                $module_settings['module_id'] = $module_id;

                $this->model_setting_module->editModule($module_id, $module_settings);

                //$json['redirect'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
            } else {
                $post = $this->request->post;
                $post['module_id'] = $this->request->get['module_id'];
                $this->model_setting_module->editModule($this->request->get['module_id'], $post);

                //$json['redirect'] = $this->url->link('extension/dc_gradient_hero/module/dc_gradient_hero', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
            }

			$json['success'] = $this->language->get('text_success');
		} else {
            if (!isset($json['error']['warning'])) {
                $json['error']['warning'] = $this->language->get('error_warning');
            }
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    protected function validate(): bool {
        if (!$this->user->hasPermission('modify', 'extension/dc_gradient_hero/module/dc_gradient_hero')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((mb_strlen($this->request->post['name']) < 3) || (mb_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    public function install(): void {
        $this->load->model('setting/setting');

        $this->model_setting_setting->editSetting('module_dc_gradient_hero', array(
            'module_dc_gradient_hero_captcha_ed_pc' => $this->generateRandomString(8), 
            'module_dc_gradient_hero_captcha_ed_ec' => $this->generateRandomString(8)  // Secure string for encrypt/decrypt module_id settings.
        ));
    }

    public function uninstall(): void {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_dc_gradient_hero');
    }

    private function generateRandomString(int $length = 16): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, $characters_length - 1)];
        }

        return $string;
    }
}