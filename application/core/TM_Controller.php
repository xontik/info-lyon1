<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class TM_Controller extends CI_Controller {
    private $viewData;
    private $pageName;
    protected $data;

    public function __construct()
    {
        parent::__construct();

        if(!isset($_SESSION['userType'])) {
            show_404();
        }

        $this->viewData = array();
        $this->data = array();
    }

    public function _remap($firstArg, $args = array())
    {
        $args = array_merge(array($firstArg), $args);
        $this->pageName = strtolower($this->router->fetch_class());

        $this->_fillData($_SESSION['userType'], $args[0]);

        if (in_array($_SESSION['userType'], $this->config->item('userTypes'))) {
            $this->_userCall($_SESSION['userType'], $args);
        } else {
            redirect(base_url('user/disconnect'));
        }
    }

    private function _fillData($userType, $page)
    {
        if (isset($_SESSION['pageNotif'])) {
            // Keep them to be able to load them with ajax
            $this->session->keep_flashdata('pageNotif');
        }

        $this->viewData['notifications'] = array_merge(
            isset($_SESSION['sessionNotif']) ? $_SESSION['sessionNotif'] : array(),
            $this->Notifications->getAll($_SESSION['userId'])
        );

        $resourceName = ucfirst($userType) . '/' . $this->pageName . ($page !== 'index' ? '_' . $page : '');
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/css/' . $resourceName . '.css')) {
            $this->viewData['css'][] = $resourceName;
        }
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/js/' . $resourceName . '.js')) {
            $this->viewData['js'][] = $resourceName;
        }
        $this->viewData['pageName'] = $this->pageName;
    }

    private function _userCall($userType, $args)
    {
        $functionName = $userType . '_' . $args[0];
        if (method_exists($this, $functionName)) {
            $view = ucfirst($userType) . '/' . $this->pageName . ($args[0] !== 'index' ? '_' . $args[0] : '');
            $this->setData('view', $view);

            call_user_func_array(array($this, $functionName), array_slice($args, 1));
        } else if (method_exists($this, $userType . '_index')) {
            $this->setData('view', ucfirst($userType) . '/' . $this->pageName);

            call_user_func_array(array($this, $userType . '_index'), $args);
        } else {
            show_404();
        }
    }

    /**
     * Loads the page and send it to the user.
     *
     * @param string $pageTitle Title of the page
     */
    protected function show($pageTitle = '')
    {
        if ($pageTitle !== '') {
            $this->viewData['title'] = $pageTitle;
        }

        $this->viewData['data'] = $this->data;
        show($this->viewData['view'], $this->viewData);
    }

    /**
     * Modify the data transmitted to the view.
     * If the data is a string, replace it by $content.
     * It the data is an array and the content is empty, delete the data,
     * if it's not, add it to the array.
     *
     * @param string|array $data The data to modify
     * @param string $content The content of the data
     * @return bool If the data doesn't exist or is unmodifiable, return false, else true
     */
    protected function setData($data, $content = '') {
        $dataViewKeys = $this->config->item('dataViewKeys');

        if (is_array($data)) {
            $ret = true;
            foreach($data as $key => $value) {
                $ret = $ret && $this->setData($key, $value);
            }
            return $ret;
        }
        else {
            if (!array_key_exists($data, $dataViewKeys)
                || $dataViewKeys[$data] === 'unmodifiable'
            ) {
                trigger_error('Inexistant or unmodifiable key : ' . $data);
                return false;
            }

            switch ($dataViewKeys[$data]) {
                case 'array':
                    if (empty($content)) {
                        unset($this->viewData[$data]);
                    } else {
                        $this->viewData[$data][] = $content;
                    }
                    break;
                case 'string':
                    $this->viewData[$data] = $content;
                    break;
                default:
                    trigger_error('Unknown data type : ' . $dataViewKeys[$data]);
            }
            return true;
        }
    }

    protected function getPageName() {
        return $this->pageName;
    }
}
