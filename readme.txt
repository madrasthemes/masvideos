=== MAS Videos ===
Contributors: madrasthemes
Tags: videos, movies, tv shows, tmdb
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.3.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

MAS Videos is a free plugin that allows you to create and list movies, videos and TV shows.

== Description ==

MAS Videos is a free plugin that allows you to create and list movies, videos and TV shows. Have you ever wished to build a website similar to YouTube, Netflix or a simple video based website using WordPress ? MAS Videos allows you to do just that. You can create a website similar to YouTube, Netflix or any video based website.

= MAS Videos' Features =

MAS Videos is free and has many advanced features to suit your needs. 

* You can add, edit and manage Movies, TV Shows and Videos just like you manage your posts
* Create and assign attributes such as Director, Actor, etc to your Movies, TV Shows and Videos
* Use WordPress' embed feature to embed Videos, Movies and TV Shows
* Gutenberg blocks for Movies, TV Shows and Videos
* Shortcodes and Widgets to display Movies, TV Shows and Videos
* Manage Playlist using your WP user account. 
* Allows registered users to upload their own videos just like YouTube.

== Installation ==

= Minimum Requirements =

* WordPress 6.4 or greater
* PHP version 7.4 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of MAS Videos, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “MAS Videos” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Will MAS Videos work with my theme? =

Yes; MAS Videos will work with any theme, but may require some styling to make it match nicely.

= Where can I report bugs or contribute to the project? =

Bugs can be reported either in our support forum or preferably on the [MAS Videos GitHub repository](https://github.com/madrasthemes/masvideos/issues).

= How do I report a security vulnerability? =

Please do not report security issues in public issues or forums. Use private vulnerability reporting on the GitHub repository (open the repository's Security tab and choose "Report a vulnerability"). If private reporting is not available, contact the MadrasThemes team via https://madrasthemes.com/ and request a secure channel before sharing any details. We will coordinate a fix and disclosure with you.

= Can I contribute? =

Yes you can! Join in on our [GitHub repository](https://github.com/madrasthemes/masvideos/) :)

== Documentation ==

All of our online documentation can be found on [our documentation site](https://docs.madrasthemes.com/masvideos/).

== Screenshots ==

1. A movie archive (grid).
2. A single movie page.
3. A video archive (grid).
4. A single video page.
5. A TV show archive (grid).
6. A single TV show page.
7. A single Episode page.
8. MAS Videos movies admin.
9. Movie data panel.
10. MAS Videos videos admin.
11. Video data panel.
12. MAS Videos TV shows admin.
13. TV Show data panel.
14. MAS Videos episodes admin.
15. Episode data panel.

== External services ==

This plugin can connect to The Movie Database (TMDB) to import movie, TV show and person data. This happens only when a site administrator uses the built-in TMDB importer and has entered a TMDB API key in the plugin settings. The feature is optional and is never used on the site front end.

- When requests are sent: only during an administrator-initiated action in the WordPress admin — the TMDB importer's search/fetch step (wp-admin, Tools &rarr; Import). No requests are made on the front end or on a schedule by this feature.
- What is sent: requests go to TMDB's API over HTTPS and include the site's configured TMDB API key and the lookup parameters for that action (the search text you enter, or the TMDB id / season / episode numbers being imported, together with a language code and an "include adult" flag). This feature does not send any WordPress user, visitor or site-credential data.
- What is received: movie, TV show and person metadata (titles, descriptions, dates, credits, genres, etc.) and image URLs, which are stored as the imported posts and their media.

TMDB is a third-party service provided by TiVo Platform Technologies LLC, governed by its own terms and privacy policy:

- Terms of Use: https://www.themoviedb.org/terms-of-use
- API Terms of Use: https://www.themoviedb.org/api-terms-of-use
- Privacy Policy: https://www.themoviedb.org/privacy-policy

This product uses TMDB and the TMDB APIs but is not endorsed, certified, or otherwise approved by TMDB.

== Changelog ==

= 1.3.5 - 2026-10-20 =
* Security : Hardened access control and CSRF protection across AJAX, account and admin request handlers.
* Security : Restricted attribute taxonomy handling and added sanitization of video/cast/crew metadata.
* Security : Confined email-template file operations to the active theme and prepared all database queries.
* Security : Safer redirects, hardened session cookies, and TMDB requests over HTTPS with host restriction.
* Compliance : WordPress.org Plugin Check remediation - output escaping, internationalization and deprecated-API fixes.
* Credit : Thanks to JunHee CHO (reported via WPScan) for responsibly disclosing security issues.

= 1.3.4 - 2026-02-09 =
* Fix : Issues in readme.txt file

= 1.3.3 - 2026-02-09 =
* Feature : Missing access control for Playlist
* Fix     : Import issues in TMDB TV shows importer

= 1.3.2 - 2025-12-09 =
* Fix : TMDB Importer PHP comparability

= 1.3.1 - 2025-3-06 =
* Fix : Playlist title update issue

= 1.3.0 - 2024-12-04 =
* Compatible with php 8.x for MAS Videos

= 1.2.9 - 2024-09-14 =
* Feature - WordPress 6.6 compatible
* Fix - Add attributes to class methods


= 1.2.8 - 2023-04-01 =
* Fix – Editor issues
* Tested upto 6.2

= 1.2.7 - 2022-01-31 =
* Corrected text issues
* Tested upto 5.9

= 1.2.6 - 2020-03-29 =
* Tested upto PHP version 5.7

= 1.2.5 - 2020-01-29 =
* Tweak – Attribute functions updated
* Fix – Structure data updated

= 1.2.4 - 2020-01-15 =
* Fix – Error fixed with tags filters widgets
* Fix – Structure data schema.org error fixed

= 1.2.3 - 2020-12-16 =
* Tweak – CSV importer file handler functions updated
* Fix – TV Show with single episode seasons tab issue fixed
* Fix – Person Birth Date issue fixed

= 1.2.2 - 2020-09-21 =
* Tweak – Display TV Show name in Episode archive
* Fix – TMDB import language issue fixed

= 1.2.1 - 2020-08-07 =
* Fix – PHP warning issue fixed

= 1.2.0 - 2020-08-07 =
* Enhancement – Video upload form enable user create new tags
* Enhancement – Send Email for new user registration
* Enhancement – Blocks tag filter added
* Tweak – Shortcode tag filter updated
* Fix – Person attributes term issue fixed
* Fix – Person movies assign issue fixed
* Fix – Shortcode pagination issue fixed
* Fix – TMDB TV Show import issues fixed
* Fix – PHP warning issue fixed
* Fix – Textdomain issues fixed

= 1.1.9 - 2020-04-13 =
* Enhancement – Person & Playlist permalink settings added
* Tweak – Account functions updated
* Tweak – More filters added
* Fix – Blocks translation added

= 1.1.8 - 2020-03-12 =
* Enhancement – TMDB import language option added
* Tweak – Active season tab based on episode
* Fix – Single video page missing sections added
* Fix – Video gallery flip issue fixed

= 1.1.7 - 2020-02-28 =
* Tweak – CPTs author support added
* Tweak – Submit video form updated
* Fix – Episode navigation issue fixed
* Fix – Sources field show/hide issues fixed

= 1.1.6 - 2020-02-06 =
* Enhancement – My account profile edit page added
* Tweak – Playlist visibility label updated
* Fix – Twenty Twenty theme compatibility issues fixed

= 1.1.5 - 2020-01-17 =
* Fix – Person single page issues fixed
* Fix – Person row setting issues fixed
* Fix – TMDB importer description issue fixed

= 1.1.4 - 2019-11-25 =
* Fix – Movies genre filter widget issue fixed
* Fix – Videos block Top rated option removed
* Fix – TV Show importer issue fixed

= 1.1.3 - 2019-10-25 =
* Fix – Filter widgets default permalink issue fixed
* Fix – Structured data warnings fixed
* Fix – TV Show importer issue fixed

= 1.1.2 - 2019-10-10 =
* Tweak – Video upload page updated
* Fix – Deleted person error fixed in Movie, TV Show
* Fix – Catelog ordering default permalink issue fixed

= 1.1.1 - 2019-07-20 =
* Fix – Episode sources warning issue fixed

= 1.1.0 - 2019-07-19 =
* Enhancement – TMDB Importer added
* Enhancement – Persons added for Cast and Crew
* Tweak – Account page registration option added

= 1.0.8 - 2019-06-18 =
* Tweak – Video display function updated
* Fix – Year filter issue fixed

= 1.0.7 - 2019-06-14 =
* Fix – Embed video escape issue fixed

= 1.0.6 - 2019-05-31 =
* Tweak - Sources "Play now" button scroll to player added
* Tweak - TV Show single page tabs added
* Fix – Movies block orderby date issue fixed
* Fix – Movies archive orderby latest issue fixed

= 1.0.5 - 2019-05-27 =
* Tweak - jQuery BlockUI added
* Fix – Source url issue fixed

= 1.0.4 - 2019-05-25 =
* Enhancement – Upload page added
* Fix – Sources issue fixed

= 1.0.3 - 2019-05-16 =
* Enhancement – My Account page added
* Enhancement – Video gallery feature added
* Performance – Movie year filter query updated
* Tweak - Deprecate playlist settings and playlist pages in favor of My Account page added

= 1.0.2 - 2019-05-07 =
* Fix – Movie reviews issue fixed
* Fix – Translation issue fixed
* Tweak – Login redirect added
* Enhancement – Permalink settings added

= 1.0.1 - 2019-04-30 =
* Fix – Star ratings style issue fixed
* Fix – Review HTML validation issue fixed
* Fix – Gutenberg blocks post selector issue fixed
* Tweak – Featured image displyed in admin for CPTs
* Enhancement – Filter Videos by Tags widget added
* Enhancement – History feature added
* Enhancement – Sources feature added

= 1.0.0 - 2019-04-11 =
* Initial Release
