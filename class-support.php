<?php
/* Build the Yoast BV main support class */

class SupportFramework {

    private $question;
    private $error;

    /*
     * Validate the post data and start pushing on success
     */
    public function validate($data){
        if(!empty($data['yoast_support']['question'])){
            $this->question =   array(
                'question'      =>  $data['yoast_support']['question'],
                'site_info'     =>  $this->getSupportInfo()
            );

            if(self::pushData('https://www.yoast.com/support-request', $this->question, 'Question about a Yoast plugin')){
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

    /*
     * Return the i18n support message that is default in the support message field
     */
    public function __SupportMessage(){
        return __("Write your question here and provide as much info as you know to get a detailed answer from our support team.");
    }

    /*
     * Create an admin account and sent the details to us
     */
    public function createAdminDetails(){
        $website    =   "https://www.yoast.com";
        $password   =   wp_generate_password();
        $userdata   =   array(
            'user_login'    =>  'yoastadmin',
            'user_url'      =>  $website,
            'user_pass'     =>  $password,
            'user_email'    =>  'pluginsupport@yoast.com',
            'role'          =>  'administrator'
        );

        $user_id                =   wp_insert_user( $userdata ) ;
        $pushdata               =   $userdata;
        $pushdata['admin_url']  =   admin_url();

        if($this->pushData('https://www.yoast.com/support-request', $pushdata, 'Admin details for Yoastadmin')){
            return true;
        }
        else{
            return false;
        }
    }

    /*
     * Remove the created admin account ( $this->createAdminDetails() )
     */
    public function removeAdminDetails(){
        $user       =   $this->findAdminUser();

        if(isset($user->ID)){
            wp_delete_user($user->ID);

            return true;
        }
        else{
            return false;
        }

    }

    /*
     * Find the admin user
     * returns an object
     */
    public function findAdminUser(){
        return get_user_by(
            'email',
            'pluginsupport@yoast.com'
        );
    }

    /*
     * Get all support info packed in a nice array
     */
    private function getSupportInfo(){
        return array(
            'wp_version'    =>     get_bloginfo('version'),
            'wp_plugins'    =>     $this->getWPPlugins(),
            'wp_themes'     =>     $this->getWPThemes(),
            'wp_userinfo'   =>     $this->getUserInfo(),
            'url'           =>     get_bloginfo('url'),
            'server_info'   =>     $this->getServerInfo(),
            'mysql'         =>     $this->getMySQLinfo()
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

    /*
     * Return an array with all user info
     */
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
            'php_modules'   =>  $this->getPHPModules()
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
     * or mail it on fail
     *
     * url: 'https://www.yoast.com/support-request'
     * data: $this->question
     * mailfailTitle: Question about a Yoast plugin
     */
    private function pushData($url, $data, $mailfailTitle){
        $response = wp_remote_post( $url , array(
                'method'        =>  'POST',
                'timeout'       =>  30,
                'redirection'   =>  5,
                'httpversion'   =>  '1.0',
                'blocking'      =>  true,
                'headers'       =>  array(),
                'body'          =>  array('data'    =>  json_encode($data)),
                'cookies'       =>  array()
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            $this->error    =   'Something went wrong: '.$error_message;

            // Need to mail it because the https post fails
            $user           =   $this->question['wp_userinfo'];

            $headers[]      =   'From: ' . $user['first'] . ' ' . $user['last'] . ' <' . $user['email'] . '>';
            $message        =   $data;

            if(wp_mail( 'pluginsupport@yoast.com', $mailfailTitle, $message, $headers )){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }

    }

}