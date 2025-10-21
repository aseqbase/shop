# Shop (Project)
> aseqbase/shop

The objective is to establish a fully functional and readily accessible Shop, complete with all necessary equipment and resources, specifically designed for seamless integration within the existing framework of the aseqbase website. This involves careful planning and execution to ensure that the new Shop is not only user-friendly but also adheres to the highest standards of accessibility, allowing all visitors to navigate and utilize its features effectively. The provision of appropriate equipment is paramount to the Shop's operational efficiency, guaranteeing a smooth and productive workflow for both administrators and customers alike, ultimately enhancing the overall user experience on the aseqbase website.

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
			vendor/aseqbase/shop> composer dev:install
			```
  3. Put the destination directory of your project on the appeared step (for example, `D:\MyWebsite\shop\`)
		``` bash
		Destination Directory [D:\MyWebsite\]: D:\MyWebsite\shop\
		```
  4. Follow the steps to finish the installation of sources, database, etc.
  5. [optional] On the local server, create an optional file named `global.php` in the `shop` directory  to change your-parent-directory-name (from the `.aseq`) using:
		``` bash
		> composer shop:create global --aseq "shop" --base "your-parent-directory-name" -f
		```
		or
		``` bash
		> cd vendor/aseqbase/shop
		vendor/aseqbase/shop> composer dev:create global --aseq "shop" --base "your-parent-directory-name" -f
		```
		**Note**: Do not forget to replace "your-parent-directory-name" with your item (default `.aseq`). 
  6. Enjoy...

<h3>Using</h3>

  1. Do one of the following options:
	  	* Visit its special URL (for example, `http://shop.[my-domain-name].com`, or `http://[my-domain-name].com/shop`)
		* On the local server:
			1. Use the following command on the root directory
				``` bash
				> composer start
		  		```
		  	2. Visit the URL `localhost:8000` (for default) on the local browser
  2. Enjoy...

<h3>Updating</h3>

  1. Keep your project updated using
		``` bash
		> composer shop:update
		```
		or
		``` bash
  		> cd vendor/aseqbase/shop
		vendor/aseqbase/shop> composer dev:update
		```
  2. Follow the steps to finish the update of sources, database, etc.
  3. Enjoy...

<h3>Uninstalling</h3>

  1. Uninstall the project and the constructed database using:
		``` bash
		> composer shop:unistall
		```
		or
		``` bash
  		> cd vendor/aseqbase/shop
		vendor/aseqbase/shop> composer dev:unistall
		```
  2. Follow the steps to finish the uninstallation of sources, database, etc.
  3. Enjoy...

<h4>Creating</h4>

  1. Create a new file by a predefined template name (for example, global, config, back, router, front, user, info, etc.) using:
		``` bash
		> composer shop:create [predefined-template-name]
		```
		or
		``` bash
  		> cd vendor/aseqbase/shop
		vendor/aseqbase/shop> composer dev:create [predefined-template-name]
		```
  2. Follow the steps to finish creating the file.
  3. Enjoy...
