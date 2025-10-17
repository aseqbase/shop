# Shop (Project)
> aseqbase/shop
To create a new Shop, with all accessibility and equipment, for an aseqbase website...
## Dependencies
* <a href="http://github.com//aseqbase/aseqbase">aseqbase/aseqbase</a>
<h2>Managements</h2>
<h3>Installing</h3>

  1. Install all dependencies mentioned before
  2. Follow one of these options:
		* Open a terminal in the destination directory (for example, `D:\MyWebsite\shop\`) of the website, then install the project by:
			``` bash
			> composer create-project aseqbase/shop
			```
		* Prompts below to create a manageable project (update, uninstall, etc.):
			``` bash
			> composer require aseqbase/shop
			> cd vendor/aseqbase/shop
			> composer dev:install
			```
  3. Put the destination directory of your project on the appeared step (for example, `D:\MyWebsite\shop\`)
		``` bash
		Destination Directory [D:\MyWebsite\]: D:\MyWebsite\shop\
		```
  4. Follow the steps to finish the installation of sources, database, etc.
  * [optional] Create an optional file named `global.php` in the `shop` directory with the following script:
	  ``` php
	  <?php
		$BASE = '.aseq'; 			// (Optional) The base directory you want to inherit all properties except what you changed
		$ASEQ = 'shop'; 				// (Optional) The current subdomain sequence, or leave null if this file is in the root directory
		$SEQUENCES_PATCH = [];	// (Optional) An array to apply your custom changes in \_::$Sequences
									// newdirectory, newaseq; // Add new directory to the \_::$Sequences
									// directory, newaseq; // Update directory in the \_::$Sequences
									// directory, null; // Remove thw directory from the \_::$Sequences
	  ?>
	  ```
  5. Enjoy...
<h3>Using</h3>

  1. Do one of the following options:
	  	* Visit its special URL (for example, `http://shop.[my-domain-name].com`, or `http://[my-domain-name].com/shop`)
		* On the local server:
			1. Create or update file named `global.php` in the root directory with at least the following script:
	  			``` php
	  			<?php
					$BASE = 'shop'; // To set the base directory you want to see at the root of `localhost`
	 			 ?>
	  			```
			2. Use the following command on the root directory
				``` bash
				> composer start
		  		```
		  	3. Visit the URL `localhost:8000` on the local browser
  2. Enjoy...

<h3>Updating</h3>

  1. Keep your project updated using
		``` bash
  		> cd vendor/aseqbase/shop
		> composer dev:update
		```
  2. Follow the steps to finish the update of sources, database, etc.
  3. Enjoy...

<h3>Uninstalling</h3>

  1. Uninstall the project and the constructed database using
		``` bash
  		> cd vendor/aseqbase/shop
		> composer dev:unistall
		```
  2. Follow the steps to finish the uninstallation of sources, database, etc.
  3. Enjoy...