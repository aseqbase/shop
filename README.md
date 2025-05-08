# New Sequence aseqbase website
To create a new Shop, with all accessibility and equipment, for an aseqbase website...
## Dependencies
* <a href="http://github.com//aseqbase/aseqbase">aseqbase/aseqbase</a>
## Installations
1. Install all dependencies
  ### To make the Main Domain of aseqbase website
  2. Open a terminal in the home directory (public_html) of the website, then install the project by:
	
 	> composer create-project aseqbase/shop
  or
	
 	> composer require aseqbase/shop
  ### To make the Sub Domain of aseqbase website
  2. Open a terminal in your subdomain directory (public_html/[my-subdomain-name]/), then install the framework by:
	
 	> composer create-project aseqbase/shop
  or
	
 	> composer require aseqbase/shop
3. Create an opitonal file name `global.php` on the root directory with the bellow codes:
  ```
  <?php
  	$ASEQ = '[my-subdomain-name]'; // (Optional) The current subdomain sequence or leave null if this file is in the root directory
  	$BASE = '[the-base-directory]'; // (Optional) The base directory you want to inherit all properties except what you changed
	  $SEQUENCES_PATCH = []; 	// (Optional) An array to apply your custom changes in \_::$Sequences
								// newdirectory, newaseq; // Add new directory to the \_::$Sequences
								// directory, newaseq; // Update directory in the \_::$Sequences
								// directory, null; // Remove thw directory from the \_::$Sequences
  ?>
  ```
4. Use it through its special link (http://[my-domain-name].com or http://[my-subdomain-name].[my-domain-name].com)
5. Enjoy...