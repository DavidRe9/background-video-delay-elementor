=== Background Video Delay for Elementor – Improve LCP ===
Contributors: renovesp  
Tags: elementor, background video, performance, lcp, youtube, defer, page speed  
Requires at least: 5.0  
Tested up to: 6.5  
Requires PHP: 7.4  
Stable tag: 1.0.2  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Add delayed YouTube background videos to Elementor sections to improve LCP, page speed, and overall performance.

== Description ==

This lightweight plugin allows you to delay background YouTube videos in Elementor sections — improving page load performance and boosting LCP (Largest Contentful Paint) scores.

Key features include:

- Full mobile responsiveness  
- Admin panel to control video behavior  
- YouTube Privacy Mode support  
- Per-page or global rule targeting  
- Optimized for performance and SEO  

Perfect for landing pages and hero sections that use video backgrounds but need fast, responsive performance.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install it via the WordPress admin panel.  
2. Activate the plugin via the “Plugins” menu.  
3. Go to **WP Admin > BG Video Rules** and define your rules.  
4. In your Elementor section:  
   - **Do NOT set a background video link** in Elementor settings. The plugin will insert it automatically.

== Usage Tips ==

1. **Do not set a "video link"** in the Elementor background settings.  
   Elementor may override the plugin and insert its own video player, causing conflicts.

2. **You can still use a static background image or color** in Elementor. It acts as a fallback until the video appears.

3. **Use the correct CSS selector** for the container, like `#banner` or `.video-section`.

4. **For best results**, avoid multiple video backgrounds or large overlays.

5. **Only YouTube videos are supported** at the moment.

6. **Compatibility Notice**:  
   - The plugin is designed for Elementor but may not work perfectly with every WordPress theme or setup.  
   - CSS rules from your theme, outdated versions of Elementor/WordPress, or active cache plugins may prevent proper display.  
   - Compatibility is not guaranteed across all environments. Please test before deploying on live sites.

== Frequently Asked Questions ==

= Does this work with any theme? =  
It works with most themes, as long as you are using Elementor to build the section.

= Will this improve LCP? =  
Yes. Delaying the loading of background videos reduces render-blocking and improves perceived speed and LCP metrics.

= Can I use self-hosted videos? =  
Currently, only YouTube videos are supported.

== Changelog ==

= 1.0.2 =
* Updated plugin header for author/license consistency
* Added full GPLv2 license text
* Improved README and documentation

= 1.0.1 =  
* First public release  
* Delayed background videos with optional privacy mode  
* Fully responsive across all screen sizes  
* Admin panel with per-page rule support
* Overlay function
* Fallback image for elementor flexbox

== Upgrade Notice ==

= 1.0.1 =  
Initial release. Adds support for per-page video rules, YouTube privacy mode, and responsive delayed background videos.
