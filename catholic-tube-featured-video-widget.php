<?php
/*
Plugin Name: Catholic Tube Featured Video Widget
Plugin URI: http://www.catholic-tube.com/wordpress-widget
Description: You can use this widget on your Catholic Blog to 
Author: Travis Boudreaux
Version: 1.0
Author URI: http://www.travisboudreaux.com
*/

class CatholicTubeFeaturedVideoWidget extends WP_Widget 
{

    public $filename;

    public $fileUrl;
    
    public $downloadedContents;
    /**
     * Constructor
     *
     * @return 
     * @author Travis Boudreaux <travis@catholic-tube.com>
     */
    function CatholicTubeFeaturedVideoWidget() 
    {
         parent::WP_Widget(false, 'Catholic Tube Featured Video');
      
         $this->filename = realpath('./data.json');
         $this->fileUrl  = "http://www.catholic-tube.com/libs/data.json";
    }

    /**
     * Widget Output
     *
     * @param string $args 
     * @param string $instance 
     * @return void
     * @author Travis Boudreaux <travis@catholic-tube.com>
     */
    function widget($args, $instance) 
    {
        $videoData = $this->getVideoData();
        $this->displayFeaturedVideo($args, $instance, $videoData);
    }

    /**
     * Save Widget Form Options
     *
     * @param string $new_instance 
     * @param string $old_instance 
     * @return void
     * @author Travis Boudreaux <travis@catholic-tube.com>
     */
    function update($new_instance, $old_instance) 
    {
         $old_instance['height'] = strip_tags($new_instance['height']);
         $old_instance['width'] = strip_tags($new_instance['width']);
 
         return $old_instance;
    }

    /**
     * Display widget Form Options
     *
     * @param string $instance 
     * @return void
     * @author Travis Boudreaux <travis@catholic-tube.com>
     */
    function form($instance) 
    {
        $title = esc_attr($instance['title']);
        $this->renderField("height", "Height", $instance);
        $this->renderField("width", "Width", $instance);
    }

    /**
    * undocumented function
    *
    * @param string $args 
    * @param string $displayComments 
    * @param string $interval 
    * @return void
    * @author Travis Boudreaux <travis@catholic-tube.com>
    */
    function displayFeaturedVideo($args = array(), $instance, $videoData) 
    {    
        $args['title'] = $videoData['title'];
        $videoLink = $videoData['embed'];
        $height = $instance['height'];
        $width = $instance['width'];
        echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
        $videoData = "<iframe title=\"YouTube video player\" class=\"youtube-player\" 
                              type=\"text/html\" width=\"{$width}\" 
                              height=\"{$height}\" 
                              src=\"{$videoLink}\" frameborder=\"0\" 
                              allowFullScreen></iframe>
                              <p><a href=\"http://www.catholic-tube.com\" target=\"_blank\">Catholic Videos from Catholic Tube</a></p>";
        
        echo $videoData;
        echo $args['after_widget'];
    }

    /**
    * undocumented function
    *
    * @param string $fieldId 
    * @param string $label 
    * @param array $instance
    * @param string $type 
    * @param string $params 
    * @return void
    * @author Travis Boudreaux <travis@catholic-tube.com>
    */
    function renderField($fieldId, $label, $instance, $type="", $params=array())
    {
        $renderFieldId = $this->get_field_id($fieldId);
        $renderFieldName = $this->get_field_name($fieldId);
        $renderLabel = _e($label);
        $fieldValue = $instance[$fieldId];
        $html  = "<p>";
        $html .= "<label for=\"{$renderFieldId}\">{$renderLabel} </label> ";
        $html .= "<input type=\"text\" id=\"{$renderFieldId}\" name=\"{$renderFieldName}\" value=\"{$fieldValue}\"  />";
        $html .= "</p>";
        echo $html;
    }



    function renderYouTubeVideo()
    {
   
    } 

    function downloadVideoData()
    {
       if ($this->isVideoDataStale())
       {
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $this->fileUrl);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           $contents = curl_exec ($ch);
           curl_close ($ch);
           $this->downloadedContents = $contents;
           file_put_contents($this->fileName, $contents);
       }
    }

    /**
    * Checks to see if the featured video cache data should be updated.
    *
    * @return Boolean
    * @author Travis Boudreaux <travis@catholic-tube.com>
    */
    function isVideoDataStale()
    {
       if (!file_exists($this->fileName))
       {
           return true;
       }
       
       $cacheDuration = 60 * 60; //1 hour = 60 seconds * 60 minutes
   
       $lastModified = filemtime($this->fileName);
       $currentTime  = date('now');
       $age = $lastModified - $currentTime;
   
       return $age > $cacheDuration;
    }

    function getVideoData()
    {
        $this->downloadVideoData();
        $data = json_decode(file_get_contents($this->fileName), true);

        if (empty($data) && !empty($this->downloadedContents))
        {
            $data = json_decode($this->downloadedContents, true);
        }
        return $data;
    }
 
}


if (version_compare($wp_version, '2.8', '>=')) 
{
  add_action('widgets_init', create_function('', 'return register_widget("CatholicTubeFeaturedVideoWidget");'));
}