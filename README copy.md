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
3. Change the value of `$GLOBALS["ASEQ"]` located on `/index.php`, to the current subdomains sequence (like [my-subdomain-name]) or if this file is in the root address, leave null for that
4. Use it through its special link (http://[my-domain-name].com or http://[my-subdomain-name].[my-domain-name].com)
5. Enjoy...