<?php
/* Build the Yoast BV main support class */

class SupportFramework {

    private $question;

    public function __construct(){

    }

    public function validate($data){
        if(!empty($data['yoast_support']['question'])){
            $this->question =   array(
                'question'      =>  $data['yoast_support']['question'],
                'site_info'     =>  self::getSupportInfo()
            );

            return true;
        }
        else{
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
            'url'           =>     get_bloginfo('url'),
            'server_info'   =>     self::getServerInfo()
        );
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
                    'version'       =>  $themeInfo['Version'],
//                    'STATUS'        =>  is_theme_active($name)
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
            'php_modules'   =>  get_loaded_extensions()
        );
    }

}