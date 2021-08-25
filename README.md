# WP2Static Copy to Folder Deployment Add-on

The [WP2Static](https://wp2static.com/) WordPress plugin lets you export your WordPress site as a static HTML site. WP2Static puts the exported HTML site in the `wp-content/uploads/wp2static-processed-site` subfolder of your WordPress installation. 

With this add-on, WP2Static can then copy the HTML site from that subfolder onto a different folder on the same server. 

1. Install and activate [WP2Static](https://wp2static.com/) in your WordPress.
2. Download [`wp2static-addon-copy.zip`](release/wp2static-addon-copy.zip).
3. In in WordPress, go to Plugins > Add New > Upload Plugin, upload `wp2static-addon-copy.zip` and activate.
4. Go to WP2Static > Addons and click on "Disabled" next to "Copy Deployment" to enable the add-on.
5. Click the Configure button for "Copy Deployment".
   - **Target folder**: enter the full absolute path to the target folder on your server
   - **Clean target folder**: not implemented yet
   - **Extra source folder**: (optional) enter the full folder from which files will be copied after the deployment (e.g. it can be an `.htaccess` file or extra assets)

