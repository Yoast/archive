<?php
/* Build the Yoast BV main support class */

class SupportFramework {

    private $question;
    private $error;
    private $curl   =   true;

    public function __construct(){
        if (!extension_loaded('curl')) {
            $this->curl     =   false;
            add_settings_error( 'yoast_support-notices', 'yoast_support-error', __('To use this support form, please make sure you have enabled Curl in your php.ini.', 'yoast_support'), 'error' );
        }
    }

    public function validate($data){
        if(!empty($data['yoast_support']['question']) && $this->curl==true){
            $this->question =   array(
                'question'      =>  $data['yoast_support']['question'],
                'site_info'     =>  self::getSupportInfo()
            );

            if(self::pushData()){
                return true;
            }
            else{
                $this->error    =   __('Couldn\'t sent your question to Yoast.');

                return false;
            }
        }
        else{
            $this->error    =   __('Please fill in a question in the form below.');

            return false;
        }
    }

    public function __SupportMessage(){
        return __("Write your question here and provide as much info as you know to get a detailed answer from our support team.");
    }

    /*
     * Get all support info packed in a nice array
     */
    private function getSupportInfo(){
        return array(
            'wp_version'    =>     get_bloginfo('version'),
            'wp_plugins'    =>     self::getWPPlugins(),
            'wp_themes'     =>     self::getWPThemes(),
            'wp_userinfo'   =>     self::getUserInfo(),
            'url'           =>     get_bloginfo('url'),
            'server_info'   =>     self::getServerInfo(),
            'mysql'         =>     self::getMySQLinfo()
        );
    }

    /*
     * Central function to return the error message to the user
     */
    public function __getError(){
        return $this->error;
    }

    #######################
    # Collect all data    #
    # - Private functions #
    #######################

    /*
     * Return WP Plugins
     */
    private function getWPPlugins(){
        $plugins        =   array();
        $wp_plugins     =   get_plugins();

        if(count($wp_plugins)>=1){
            foreach($wp_plugins as $name => $pluginInfo){
                if(is_plugin_active($name)==1 && !empty(is_plugin_active($name))){
                    $plugins[]  =   array(
                        'name'          =>  $pluginInfo['Name'],
                        'plugin_uri'    =>  $pluginInfo['PluginURI'],
                        'version'       =>  $pluginInfo['Version']
                    );
                }
            }
        }

        return $plugins;
    }

    private function getUserInfo(){
        global $current_user;
        get_currentuserinfo();

        return array(
            'username'  =>      $current_user->user_login,
            'email'     =>      $current_user->user_email,
            'first'     =>      $current_user->user_firstname,
            'last'      =>      $current_user->user_lastname,
            'display'   =>      $current_user->display_name,
        );
    }

    /*
     * Return themes
     */
    private function getWPThemes(){
        $themes        =   array();
        if(function_exists('wp_get_themes')){
            $wp_themes      =   wp_get_themes();
        }
        else{
            $wp_themes      =   get_themes();
        }

        if(count($wp_themes)>=1){
            foreach($wp_themes as $name => $themeInfo){
                // Todo: check if plugin is active
                $themes[]  =   array(
                    'name'          =>  $themeInfo['Name'],
                    'version'       =>  $themeInfo['Version']
                );
            }
        }

        return $themes;
    }

    /*
     * Return the Server information
     */
    private function getServerInfo(){
        return array(
            'engine'        =>  $_SERVER['SERVER_SOFTWARE'],
            'user'          =>  $_SERVER['USER'],
            'gateway'       =>  $_SERVER['GATEWAY_INTERFACE'],
            'server_port'   =>  $_SERVER['SERVER_PORT'],
            'server_name'   =>  $_SERVER['SERVER_NAME'],
            'encoding'      =>  $_SERVER['HTTP_ACCEPT_ENCODING'],
            'php_version'   =>  phpversion(),
            'php_modules'   =>  self::getPHPModules()
        );
    }

    /*
     * Get the phpmodules with all its version numbers
     */
    private function getPHPModules(){
        $modules        =   array();

        foreach(get_loaded_extensions() as $ext){
            $modules[$ext]      =   phpversion($ext);
        }

        return $modules;
    }

    /*
     * Get all mysql info
     */
    private function getMySQLinfo(){
        return array(
            'server'      =>      mysql_get_server_info(),
            'client'      =>      mysql_get_client_info(),
            'host'        =>      mysql_get_host_info(),
            'protocol'    =>      mysql_get_proto_info(),
            'charset'     =>      mysql_client_encoding()
        );
    }

    /*
     * Sent the question to the Yoast.com webserver
     */
    private function pushData(){
        $response = wp_remote_post( 'https://www.yoast.com/support', array(
                'method'        =>  'POST',
                'timeout'       =>  30,
                'redirection'   =>  5,
                'httpversion'   =>  '1.0',
                'blocking'      =>  true,
                'headers'       =>  array(),
                'body'          =>  $this->question,
                'cookies'       =>  array()
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            $this->error    =   'Something went wrong: '.$error_message;

            return false;
        }
        else{
            echo 'Response:';
            print_r($response);

            return true;
        }

    }

}