# WP2Static Directory Deployment Add-on

The [WP2Static](https://wp2static.com/) WordPress plugin lets you export your WordPress site as a static HTML site. WP2Static puts its static converted HTML site in the `wp-content/uploads/wp2static-processed-site` subfolder of your WordPress installation before deployment. Many deployment addons target an external API, such as S3, Netlify, Cloudflare. This addon allows you to deploy a copy of the static site to a local directory on the same server as you run WP2Static.

### Use cases

 - you only have one server for your WordPress development site and static site
 - you want to use another tool to handle your deployments, by watching a target folder and syncing it (ie, `inotify` + `rsync`)

