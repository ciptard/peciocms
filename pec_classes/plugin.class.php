<?php

/**
 * pec_classes/plugin.class.php - Plugin Class
 * 
 * Defines the main Plugin class which manages all the available Plugins an their meta data.
 * 
 * LICENSE: This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option) 
 * any later version. This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 * for more details. You should have received a copy of the 
 * GNU General Public License along with this program. 
 * If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package		peciocms
 * @subpackage	pec_classes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

class PecPlugin {
    
    private $plugin_directory_name, $plugin_directory_path, $plugin_properties;
    
    static $by_properties = array(
        "title", 
        "description", 
        "author", 
        "author_email", 
        "year", 
        "license", 
        "variable", 
        "input_enabled", 
        "installation_required", 
        "main_file",  
        "area_file", 
        "area_name", 
        "class_name"
    );
    
    function __construct($directory_name) {
        $this->plugin_directory_name = $directory_name;
        $this->plugin_directory_path = PLUGIN_PATH . $directory_name . '/';
        
        $this->plugin_properties = $this->load_plugin_properties();
    }
    
    private function load_plugin_properties() {
        require($this->plugin_directory_path . PLUGIN_META_FILE);      
        return $plugin_meta;
    }
    
    public function get_directory_path() {
        return $this->plugin_directory_path;
    }
    
    public function get_directory_name() {
        return $this->plugin_directory_name;
    }
    
    public function get_property($by='title') {
        if ($by && in_array($by, self::$by_properties)) {
            return $this->plugin_properties[$by];
        }
    }
    
    public function installation_required() {
    	if ($this->plugin_properties['installation_required'] && 
    		file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALL_FILE) &&
    		file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALL_FILE)) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }
    
    public function is_installed($status_file_exists=true) {
    	if (!file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE) &&
    		file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE)) {
    		return false;
    	}
    	elseif (file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE) &&
    			!file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE)) {
    		return true;
    	}
    	elseif (!file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE) &&
    			!file_exists(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE)) {
    		return false;
    	}
    	else {
    		unlink(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE);
    		unlink(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE);
    		return false;
    	}
    }
    
    public function set_installed() {
    	return rename(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE,
               		  PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE);
    }
    
    public function set_uninstalled() {
    	return rename(PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_INSTALLED_FILE,
               		  PLUGIN_PATH . $this->plugin_directory_name . '/' . PLUGIN_UNINSTALLED_FILE);
    }
    
    public static function load($by='area_name', $data='') {
        if ($by && $data && in_array($by, self::$by_properties)) {
            $plugins = self::load();
            $plugin = false;
            
            // check which plugin shall be loaded
            foreach ($plugins as $p) {
                if ($p->get_property($by) == $data) {
                    $plugin = $p;
                    break;
                }
            }
            
            return $plugin;
        }
        else {
            $filenames = scandir(PLUGIN_PATH);
            
            // check which of the files in the plugin directory are directories and which not
            $plugin_directories = array();
            foreach ($filenames as $file) {
                if (is_dir(PLUGIN_PATH . $file) && $file != '.' && $file != '..') {
                    $plugin_directories[] = $file;
                }
            }
            
            // create the template objects and add them to an array
            $plugins = array();
            foreach ($plugin_directories as $dir) {
                $plugins[] = new PecPlugin($dir);
            }
            
            return $plugins;
        }
    }
    
    public static function exists($by='area_name', $data='') {      
        if ($by && $data && in_array($by, self::$by_properties)) {
            $plugins = self::load();
            
            $exists = false;
            foreach ($plugins as $p) {
                if ($p->get_property($by) == $data) {
                    $exists = true;
                    break;
                }
            }
            
            return $exists;
        }
        else {
            return false;
        }
    }
    
}

?>