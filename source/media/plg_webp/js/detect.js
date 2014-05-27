/**
 * Yireo Webp for Joomla!
 *
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (c) 2014 Yireo (http://www.yireo.com/)
 * @license     GNU General Public License
 */

(function(){

    // Browser has no WebP flag
    if(hasWebP == 0) {

        // Load regular images
        noWebp();

        // If no WebP-cookie is set, load it
        var matchCookie = new RegExp('[; ] webp=(0|1)');
        var hasCookie = (' ' + document.cookie).match(matchCookie);
        if(hasCookie == null) {

          // Default cookie
          document.cookie = 'webp=0';

          // WebP image-test
          var Tester = new Image();
          Tester.onload = function(WebPSupport){
            if(Tester.width > 0 && Tester.height > 0){
              document.cookie = 'webp=1';
            }
          }
          var WebPTest = 'UklGRkYAAABXRUJQVlA4IDoAAABwAgCdASoEAAQAAYcIhYWIhYSIiQIADAzdrBLeABAAAAEAAAEAAPKn5Nn/0v8//Zxn/6H3QAAAAAA=';
          Tester.src = 'data:image/webp;base64,' + WebPTest;
        }
    }
})();

function noWebp() {
  images = document.getElementsByTagName('img');
  for(var i = 0, len = images.length; i < len; i++) {
    if (images[i].hasAttribute('data-src')) {
      images[i].src = images[i].getAttribute('data-src');
    }
  }
}
