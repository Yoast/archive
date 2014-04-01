<?php
/* Build the Yoast BV main support class */

class SupportFramework {

    public function __construct(){

    }

    public function getSupportInfo(){
        $info     =   array();

        $info['WP_VERSION']      =     get_bloginfo('version');
        $info['WP_PLUGINS']      =     self::getWPPlugins();
        $info['WP_THEMES']       =     self::getWPThemes();
        $info['URL']             =     get_bloginfo('url');
        $info['SERVER_INFO']     =     self::getServerInfo();

        return $info;
    }

    public function validate(){

    }


    /*
     * Data collector functions
     */

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
                        'NAME'          =>  $pluginInfo['Name'],
                        'PLUGIN_URI'    =>  $pluginInfo['PluginURI'],
                        'VERSION'       =>  $pluginInfo['Version']
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
                    'NAME'          =>  $themeInfo['Name'],
                    'VERSION'       =>  $themeInfo['Version'],
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
        $server     =   array();
        $server['ENGINE']               =   $_SERVER['SERVER_SOFTWARE'];
        $server['USER']                 =   $_SERVER['USER'];
        $server['GATEWAY_INTERFACE']    =   $_SERVER['GATEWAY_INTERFACE'];
        $server['SERVER_PORT']          =   $_SERVER['SERVER_PORT'];
        $server['SERVER_NAME']          =   $_SERVER['SERVER_NAME'];
        $server['ENCODING']             =   $_SERVER['HTTP_ACCEPT_ENCODING'];
        $server['PHP_VERSION']          =   phpversion();
        $server['PHP_MODULES']          =   get_loaded_extensions();

        return $server;
    }

}