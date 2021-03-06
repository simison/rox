<?php

namespace Rox\Framework;

use Rox\Models\Message;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;

use \AbstractBasePage;
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use \RoxModelBase;
use \FlaglistModel;
use \Illuminate\Database\Capsule\Manager as Capsule;

class TwigView extends AbstractBasePage {

    protected $_loader;
    private $_environment;
    private $_template;
    private $_parameters;
    protected $_words;
    protected $_translator;
    private $_stylesheets = array(
         'bewelcome.css?1',
//        'bootstrap.css',
//        'select2.css',
//        'select2-bootstrap.css',
//          'minimal/screen/custom/index.css?3',
//          'minimal/screen/custom/font-awesome.min.css',
//          'minimal/screen/custom/font-awesome-ie7.min.css',
    );

    private $_lateScriptFiles = array(
        'bootstrap/bootstrap.min.js',
        'common/initialize.js',
        'select2/select2.min.js',
        'bootstrap-autohidingnavbar/jquery.bootstrap-autohidingnavbar.js',
    );

    private $_earlyScriptFiles = array(
        'common/common.js?1',
        'jquery-1.11.2/jquery-1.11.2.min.js',
    );

    public function __construct(Router $router) {
        $this->_loader = new Twig_Loader_Filesystem();
        $this->addNamespace('base');
        $this->addNamespace('macros');

        $this->_environment = new Twig_Environment(
            $this->_loader ,
            array(
                'cache' => SCRIPT_BASE . 'data/twig',
                'auto_reload' => true,
                'debug' => true,
            )
        );
        $this->_words = $this->getWords();

        $this->_translator = new Translator($_SESSION['lang'], new MessageSelector());
        if ($_SESSION['lang'] <> 'en') {
            $this->_translator->setFallbackLocales(array('en'));
        }
        $this->_translator->addLoader('database', new DatabaseLoader());
        $this->_translator->addResource('database', null, $_SESSION['lang']);
        $this->_environment->addExtension(new TranslationExtension($this->_translator));
        $this->_environment->addExtension(new RoxTwigExtension());
        if ($router != null) {
            $this->_environment->addExtension(new RoutingExtension($router->getGenerator()));
        }
        $this->_environment->addExtension(new \Twig_Extension_Debug());
    }

    /**
     * Adds a namespace for Twig templates (set path accordingly)
     *
     * @param $namespace
     * @throws \Twig_Error_Loader
     */
    public function addNameSpace($namespace) {
        $path = realpath(SCRIPT_BASE . 'templates/twig/' . $namespace);
        $this->_loader->addPath($path, $namespace);
    }

    private function _getDefaults() {
        $roxModel = new RoxModelBase();
        $member = $roxModel->getLoggedInMember();
        $loggedIn = ($member !== false);
        $messageCount = 0;
        if ($loggedIn) {
            $messageCount =
                Message::where('IdReceiver', (int)$member->id)
                    ->where('WhenFirstRead', '0000-00-00 00:00')
                    ->where('Status', 'Sent')
                    ->count();
        }
        return array(
            'logged_in' => $loggedIn,
            'messages' => $messageCount,
            'username' => $member->Username,
            'meta.robots' => 'ALL',
            'title' => 'BeWelcome'
        );
    }

    private function _getLanguages() {
        $model = new FlaglistModel();
        $langarr = array();
        foreach($model->getLanguages() as $language) {
            $lang = new \stdClass;
            $lang->NativeName = $language->Name;
            $lang->TranslatedName = $this->_words->getSilent($language->WordCode);
            $lang->ShortCode = $language->ShortCode;
            $langarr[] = $lang;
        }
        $ascending = function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return (strtolower($a->TranslatedName) < strToLower($b->TranslatedName)) ? -1 : 1;
        };
        usort($langarr, $ascending);

        return array(
            'language' => $_SESSION['lang'],
            'languages' => $langarr
        );
    }

    protected function addStylesheet($stylesheet) {
        $this->_stylesheets[] = $stylesheet;
    }

    protected function addEarlyJavascriptFile($scriptFile, $name = false) {
        if ($name) {
            $this->_earlyScriptFiles[$name] = $scriptFile;
        } else {
            $this->_earlyScriptFiles[] = $scriptFile;
        }
    }

    protected function addLateJavascriptFile($scriptFile, $name = false) {
        if ($name) {
            $this->_lateScriptFiles[$name] = $scriptFile;
        } else {
            $this->_lateScriptFiles[] = $scriptFile;
        }
    }

    protected function _getStylesheets() {
        return array(
            'stylesheets' => $this->_stylesheets
        );
    }

    protected function _getEarlyJavascriptFiles() {
        return array(
            'earlyScriptFiles' => $this->_earlyScriptFiles
        );
    }

    protected function _getLateJavascriptFiles() {
        return array(
            'lateScriptFiles' => $this->_lateScriptFiles
        );
    }

    public function setTemplate($template, $namespace = false, $parameters = array()) {
        if ($namespace) {
            $this->addNameSpace($namespace);
            $this->_template = '@' . $namespace . '/' . $template;
        } else {
            $this->_template = $template;
        }
        $this->setParameters($parameters);
    }

    /**
     * Set the parameters to be used during rendering
     *
     * @param array $parameters
     */
    public function setParameters($parameters) {
        $finalParameters = array_merge(
            $parameters,
            $this->_getStylesheets(),
            $this->_getLanguages(),
            $this->_getEarlyJavascriptFiles(),
            $this->_getLateJavascriptFiles(),
            $this->_getDefaults()
        );
        $this->_parameters = $finalParameters;
    }

    /**
     * Actually renders the page
     *
     * @return string
     */
    public function render() {
        return $this->_environment->render($this->_template, $this->_parameters);
    }
}