<?php
/**
 * Joomla! System plugin - WebP
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );

/**
 * WebP System Plugin
 */
class plgSystemWebP extends JPlugin
{
    /**
     * Event onAfterDispatch
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterInitialise()
    {
        // Only continue in the frontend
        $application = JFactory::getApplication();
        if($application->isSite() == false) {
            return false;
        }

        // Check for WebP support
        $webp_support = false;

        // Check for the "webp" cookie
        if(isset($_COOKIE['webp']) && $_COOKIE['webp'] == 1) {
            $webp_support = true;

        // Check for Chrome 9 or higher
        } elseif(preg_match('/Chrome\/([0-9]+)/', $_SERVER['HTTP_USER_AGENT'], $match) && $match[1] > 8) {
            $webp_support = true;
        }

        if($webp_support == true) {
            JFactory::getConfig()->set('webp', true);
            JHtml::_('jquery.framework');
        }
    }

    /**
     * Event onAfterRender
     *
     * @param null
     * @return null
     */
    public function onAfterRender()
    {
        // Only continue in the frontend
        $application = JFactory::getApplication();
        if(!$application->isSite()) {
            return false;
        }

        // Check the WebP flag
        if(JFactory::getConfig()->get('webp') == false) {
            return false;
        }

        // Check for the cwebp binary
        $cwebp = $this->params->get('cwebp');
        if(empty($cwebp)) {
            return false;
        }

        // Check for PHP-exec
        if(function_exists('exec') == false) {
            return false;
        }

        // Get the body and fetch a list of files
        $body = JResponse::getBody();

        $html = array();
        if(preg_match_all('/\ src=\"([^\"]+)\.(png|jpg|jpeg)\"/', $body, $matches)) {

            $imageList = array();
            foreach($matches[0] as $index => $match) {

                $imageUrl = $matches[1][$index].'.'.$matches[2][$index];
                if(preg_match('/^(http|https):\/\//', $imageUrl) && strstr($imageUrl, JURI::root())) {
                    $imageUrl = str_replace(JURI::root(), '', $imageUrl);
                }

                $imagePath = JPATH_ROOT.'/'.$imageUrl;
                if(is_file($imagePath)) {

                    // Construct the webP image
                    $webpPath = preg_replace('/\.(png|jpg|jpeg)$/', '.webp', $imagePath);

                    // Check if we need to create a WebP image (if it doesn't exist yet, or if the original image is modified)
                    if(is_file($webpPath) == false || filemtime($imagePath) > filemtime($webpPath)) {

                        // Detect alpha-transparency in PNG-images and skip it
                        if(preg_match('/\.png$/', $imagePath)) {
                            $imageContents = @file_get_contents($imagePath);
                            $colorType = ord(@file_get_contents($imagePath, NULL, NULL, 25, 1));
                            if($colorType == 6 || $colorType == 4) {
                                continue;
                            } elseif(stripos($imageContents, 'PLTE') !== false && stripos($imageContents, 'tRNS') !== false) {
                                continue;
                            }
                        }

                        exec("$cwebp -quiet $imagePath -o $webpPath");
                    }

                    // Only replace the WebP image if it exists
                    if(is_file($webpPath) && filesize($webpPath) > 0) {

                        // Add the image to the list
                        $image = $matches[1][$index].'.'.$matches[2][$index];
                        $webpImage = preg_replace('/\.(png|jpg|jpeg)$/', '.webp', $image);
                        $imageList[md5($image)] = array('orig' => $image, 'webp' => $webpImage);

                        // Change the image
                        $htmlTag = $matches[0][$index];
                        $newHtmlTag = str_replace('src="'.$image.'"', 'data-img="'.md5($image).'"', $htmlTag);
                        $body = str_replace($htmlTag, $newHtmlTag, $body);
                    }
                }
            }

            if(!empty($imageList)) {
                $html[] = '<script type="text/javascript">';
                $html[] = 'if(webpReplacements == null) { var webpReplacements = new Object(); }';
                foreach($imageList as $name => $value) {
                    $html[] = 'webpReplacements[\''.$name.'\'] = '.json_encode($value);
                }
                $html[] = '</script>';
            }
        }

        $html[] = '<script src="'.JURI::root().'media/plg_webp/js/jquery.cookie.js" type="text/javascript"></script>';
        $html[] = '<script src="'.JURI::root().'media/plg_webp/js/detect.js" type="text/javascript"></script>';
        $html = implode("\n", $html)."\n";
        $body = str_replace('</body>', $html.'</body>', $body);
        JResponse::setBody($body);
    }
}
