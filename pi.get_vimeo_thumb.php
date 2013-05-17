<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get_vimeo_thumb Class
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Garrett Henderson
 * @copyright   Copyright (c) 2013, Garrett Henderson
 * @link        
 */

$plugin_info = array(
	'pi_name'         => 'Get Vimeo Thumbnail',
	'pi_version'      => '1.0',
	'pi_author'       => 'Garrett Henderson',
	'pi_author_url'   => 'http://garretthenderson.com/',
	'pi_description'  => 'Returns the URL for a Vimeo video thumbnail',
	'pi_usage'        => Get_vimeo_thumb::usage()
);

class Get_vimeo_thumb {

	public $return_data = "";
	public $cache_path;

	// --------------------------------------------------------------------

    public function __construct()
    {
        $this->EE =& get_instance();
		$this->cache_path = $this->EE->config->item('get_vimeo_thumb_cache_path') ? $this->EE->config->item('get_vimeo_thumb_cache_path') : APPPATH . 'cache/get_vimeo_thumb/';
		// add trailing slash to cache path if not exist
		if (substr($this->cache_path,-1,1) != '/') { $this->cache_path .= '/'; }

    }

    /**
	 * Get_vimeo_thumb
	 *
	 * This function returns the URL of a Vimeo video
	 *
	 * @access public
	 * @return mixed Will return the new tagdata (str) on success or no_results on failure
	 */
    public function pair()
    {
    	//print_r($this->EE->config->config['get_vimeo_thumb']);
    	$size = $this->EE->TMPL->fetch_param('size','small');
        $id = $this->EE->TMPL->fetch_param('id');
        $refresh = $this->EE->TMPL->fetch_param('refresh') ? $this->EE->TMPL->fetch_param('refresh') : 7;

        if ($id == "")
        {
        	$this->EE->TMPL->log_item( '&nbsp;&nbsp;***&nbsp;&nbsp;Get Vimeo Thumb Debug: No Vimeo ID specified' );
        	return;
        }

        $data_file_path = $this->cache_path . $id . ".xml";

        // check if we have the xml for this video cached and is not expired
        if (file_exists($data_file_path) && (time() - filemtime($data_file_path) < intval($refresh) * 24 * 60 * 60))
        {
        	$this->EE->TMPL->log_item( '&nbsp;&nbsp;***&nbsp;&nbsp;Get Vimeo Thumb Debug: Using cached Vimeo XML data' );
        	$data = @simplexml_load_file($data_file_path);
        }
        else
        {
        	$data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $id . ".xml");
        	// make cache dir if not exist
        	if (!file_exists($this->cache_path))
        	{
        		mkdir($this->cache_path);
        	}
        	// if the xml file loaded write new (or overwrite expired)
        	if ($data)
        	{
        		$data->asXML($data_file_path);
        	}
        }

        if ($data)
        {
        	$variables = array();

	        switch ($size)
	        {
	        	case "small":
	        		$variables['vimeo_thumb_url'] = (string) $data->video->thumbnail_small;
	        		break;
	        	case "medium":
		        	$variables['vimeo_thumb_url'] = (string) $data->video->thumbnail_medium;
		        	break;
	        	case "large":
	        		$variables['vimeo_thumb_url'] = (string) $data->video->thumbnail_large;
	        		break;
                default:
                    $this->EE->TMPL->log_item( '&nbsp;&nbsp;***&nbsp;&nbsp;Get Vimeo Thumb Debug: No size parameter specified' );
                    break;
	        }

	        return $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $variables);
        }
        else
        {
        	$this->EE->TMPL->log_item( '&nbsp;&nbsp;***&nbsp;&nbsp;Get Vimeo Thumb Debug: No Vimeo XML file found, Ensure valid Vimeo ID' );
        	return $this->EE->TMPL->no_results();
        }
    }
    
    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
    {
        ob_start();  ?>

The Get Vimeo Thumb plugin outputs the URL of a Vimeo video thumbnail.

    {exp:get_vimeo_thumb:pair id="vimeo_id" size="small|medium|large" refresh="7"}
	
	{vimeo_thumb_url}

    {/exp:get_vimeo_thumb}

id: the vimeo video id
size: small|medim|large
refresh: number of days to cache the video xml data. Default is 7.

Config file overrides:

$config['get_vimeo_thumb_cache_path'] = 'YOUR_CUSTOM_PATH';

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
    // END
}

/* End of file pi.get_vimeo_thumb.php */
/* Location: ./system/expressionengine/third_party/get_vimeo_thumb/pi.get_vimeo_thumb.php */