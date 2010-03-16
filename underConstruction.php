<?php 
/*
 Plugin Name: Under Construction
 Plugin URI: http://www.masseltech.com/
 Description: Makes it so your site can only be accessed by users who log in. Useful for developing a site on a live server, without the world being able to see it
 Version: 1.02
 Author: Jeremy Massel
 Author URI: http://www.masseltech.com/
 */

/*
 This file is part of underConstruction.
 underConstruction is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 underConstruction is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with underConstruction.  If not, see <http://www.gnu.org/licenses/>.
 */

?>
<?php 
class underConstruction
{
    var $installedFolder = "";
    var $mainOptionsPage = "underConstructionMainOptions";
    
    function __construct()
    {
        $this->installedFolder = basename(dirname(__FILE__));
    }
    
    function underConstruction()
    {
        $this->__construct();
    }
    
    function getMainOptionsPage()
    {
        return $this->mainOptionsPage;
    }
    
    function underConstructionAdminInit()
    {
        /* Register our script. */
        wp_register_script('underConstructionJS', WP_PLUGIN_URL.'/'.$this->installedFolder.'/underconstruction.min.js');
    }
    
    function uc_changeMessage()
    {
        require_once ('ucOptions.php');
    }
    
    function uc_adminMenu()
    {
        /* Register our plugin page */
        $page = add_options_page('Under Construction Settings', 'Under Construction', 8, $this->mainOptionsPage, array($this, 'uc_changeMessage'));
        
        /* Using registered $page handle to hook script load */
        add_action('admin_print_scripts-'.$page, array($this, 'underConstructionEnqueueScripts'));
        
    }
    
    function underConstructionEnqueueScripts()
    {
        /*
         * It will be called only on your plugin admin page, enqueue our script here
         */
        wp_enqueue_script('scriptaculous');
        wp_enqueue_script('underConstructionJS');
    }
    
    function uc_overrideWP()
    {
        if ($this->pluginIsActive())
        {
            if (!is_user_logged_in())
            {
                //send a 503 if the setting requires it
                if (get_option('underConstructionHTTPStatus') == 503)
                {
                    header('HTTP/1.1 503 Service Unavailable');
                }
                
                if ($this->displayStatusCodeIs(0)) //they want the default!
                {
                    require_once ('defaultMessage.php');
                    displayDefaultComingSoonPage();
                    die();
                }
                
                if ($this->displayStatusCodeIs(1)) //they want the default with custom text!
                {
                    require_once ('defaultMessage.php');
                    displayComingSoonPage($this->getCustomPageTitle(), $this->getCustomHeaderText(), $this->getCustomBodyText());
                    die();
                }
                
                if ($this->displayStatusCodeIs(2)) //they want custom HTML!
                {
                    echo html_entity_decode($this->getCustomHTML());
                    die();
                }
            }
        }
    }
    
    function getCustomHTML()
    {
        return stripslashes(get_option('underConstructionHTML'));
    }
    
    function uc_remove()
    {
        delete_option('underConstructionHTML');
    }
    
    function pluginIsActive()
    {
    
        if (!get_option('underConstructionActivationStatus')) //if it's not set yet
        {
            return false;
        }
        
        if (get_option('underConstructionActivationStatus') == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function httpStatusCodeIs($status)
    {
        if (!get_option('underConstructionHTTPStatus')) //if it's not set yet
        {
            update_option('underConstructionHTTPStatus', 200); //set it
        }
        
        if (get_option('underConstructionHTTPStatus') == $status)
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
    
    function displayStatusCodeIs($status)
    {
        if (!get_option('underConstructionDisplayOption')) //if it's not set yet
        {
            update_option('underConstructionDisplayOption', 0); //set it
        }
        
        if (get_option('underConstructionDisplayOption') == $status)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function getCustomPageTitle()
    {
        if (get_option('underConstructionCustomText') != false)
        {
            $fields = get_option('underConstructionCustomText');
            return stripslashes($fields['pageTitle']);
        }
        else
        {
            return 'empty';
        }
    }
    
    function getCustomHeaderText()
    {
        if (get_option('underConstructionCustomText') != false)
        {
            $fields = get_option('underConstructionCustomText');
            return stripslashes($fields['headerText']);
        }
        else
        {
            return 'empty';
        }
    }
    
    function getCustomBodyText()
    {
        if (get_option('underConstructionCustomText') != false)
        {
            $fields = get_option('underConstructionCustomText');
            return stripslashes($fields['bodyText']);
        }
        else
        {
            return 'empty';
        }
    }

    
}

$underConstructionPlugin = new underConstruction();

add_action('template_redirect', array($underConstructionPlugin, 'uc_overrideWP'));
register_deactivation_hook(__FILE__, array($underConstructionPlugin, 'uc_remove'));

add_action('admin_init', array($underConstructionPlugin, 'underConstructionAdminInit'));
add_action('admin_menu', array($underConstructionPlugin, 'uc_adminMenu'));


function underConstructionPluginLinks($links, $file)
{
    global $underConstructionPlugin;
    if ($file == basename(dirname(__FILE__)).'/'.basename(__FILE__) && function_exists("admin_url"))
    {
        //add settings page
        $manage_link = '<a href="'.admin_url('options-general.php?page='.$underConstructionPlugin->getMainOptionsPage()).'">'.__('Settings').'</a>';
        array_unshift($links, $manage_link);
        
     

        
    }
    return $links;
}

add_filter('plugin_action_links', 'underConstructionPluginLinks', 10, 2);

?>
