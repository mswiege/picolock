<?php

/**
 * Provides a Password request screen in front of all Pico CMS pages
 *
 * @author Maik Wiege
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 0.1
 */
class PicoLock extends AbstractPicoPlugin
{
    /**
     * API version used by this plugin
     *
     * @var int
     */
    const API_VERSION = 3;

    /**
     * path to this plugin directory
     *
     * @see PicoLock::onConfigLoaded()
     */
    private $plugin_path;

    /**
     * PicoLock password
     */
    private $password;

    /**
     * Triggered after Pico has read its configuration
	 * Here we load the password from the config
     *
     * @param array &$config array of config variables
     * @see Pico::getBaseUrl()
     * @see Pico::isUrlRewritingEnabled()
     *
     * @see Pico::getConfig()
     */
    public function onConfigLoaded(array &$config)
    {
        // path to the plugin, used for rendering templates
        $this->plugin_path = dirname(__FILE__);
        // check configuration for password
        if (isset($config['PicoLock']['password']) && !empty($config['PicoLock']['password'])) {
            $this->password = $config['PicoLock']['password'];
        }

		// load text ressources
        if (isset($config['PicoLock']['enterPasswordMessage']) && !empty($config['PicoLock']['enterPasswordMessage'])) {
            $this->enterPasswordMessage = $config['PicoLock']['enterPasswordMessage'];
        } else {
			$this->enterPasswordMessage = 'Please enter the password:';
		}
        if (isset($config['PicoLock']['wrongPasswordMessage']) && !empty($config['PicoLock']['wrongPasswordMessage'])) {
            $this->wrongPasswordMessage = $config['PicoLock']['wrongPasswordMessage'];
        } else {
			$this->wrongPasswordMessage = 'Wrong password. Please try again.';
		}

        // check for session
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * Triggered before Pico renders the page
	 * Here we check if the password has been provided in this session
	 * and if not, how the password screen to the user
     *
     * @param string &$templateName file name of the template
     * @param array  &$twigVariables template variables
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @see  DummyPlugin::onPageRendered()
     * @uses $_POST['password']
     */
    public function onPageRendering(&$templateName, array &$twigVariables)
    {
		// check if no password exists
		if (!$this->password) {
			// render the login view
            $this->showLoginScreen("No password set!");
			// don't continue to render template
			exit;
		}

		// if no current session exists,
		if (!isset($_SESSION['picoLock_logged_in']) || !$_SESSION['picoLock_logged_in']) {
			// check that user is POSTing a password
			if (isset($_POST['password'])) {
				// does the password match the hashed password?
				if (hash('sha512', $_POST['password']) == $this->password) {
					// login success
					$_SESSION['picoLock_logged_in'] = true;
					// reload the page (otherwise we get annoying "resubmit form?" message from browser on page refreshs
					header('Location: ' . $this->pico->getPageUrl($this->pico->getRequestUrl()));
				} else {
					// login failure
					$this->showLoginScreen($this->wrongPasswordMessage);
					// don't continue to render template
					exit;
				}
			} else {
				$this->showLoginScreen($this->enterPasswordMessage);
				// don't continue to render template
				exit;
			}
		}

		// valid session exists, render the requested page.
    }
	
	private function showLoginScreen($msg){
		$loader = new Twig_Loader_Filesystem($this->plugin_path);
		$this->getPico()->getTwig()->setLoader($loader);
		$twigVariables['login_message'] = $msg;
		$loader = new Twig_Loader_Filesystem($this->plugin_path);
		$this->getPico()->getTwig()->setLoader($loader);
		// render the login view
		echo $this->getPico()->getTwig()->render('views/login.twig', $twigVariables);
	}		

    /**
     * Exit from admin session
     */
    private function doLogout()
    {
        // destroy the current session
        session_destroy();
        // redirect to the login page...
        header('Location: ' . $this->pico->getPageUrl());
        // don't continue to render template
        exit;
    }
}
