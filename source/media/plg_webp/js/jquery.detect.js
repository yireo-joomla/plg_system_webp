/**
 * Yireo Webp for Joomla!
 *
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (c) 2015 Yireo (http://www.yireo.com/)
 * @license     GNU General Public License
 */
  
var hasWebp = false;

if(jQuery.cookie('webp') == 1) {
  hasWebp = true;
}

if(hasWebp == false) {
  var Tester = new Image();
  Tester.onload = function(){
    if(Tester.width > 0 && Tester.height > 0){
      document.cookie = 'webp=1';
      hasWebp = true;
    }
  };

  var WebPTest = 'UklGRkYAAABXRUJQVlA4IDoAAABwAgCdASoEAAQAAYcIhYWIhYSIiQIADAzdrBLeABAAAAEAAAEAAPKn5Nn/0v8//Zxn/6H3QAAAAAA=';
  Tester.src = 'media/plg_webp/images/test.webp';
}

if (typeof webpReplacements !== 'undefined') {
  jQuery('img').each(function(img) {
    dataImg = jQuery(this).attr('data-img');
    if(dataImg && webpReplacements[dataImg]) {
      data = webpReplacements[dataImg];
      if(hasWebp) {
        jQuery(this).attr('src', data.webp);
      } else {
        jQuery(this).attr('src', data.orig);
      }
    }
  });
}
