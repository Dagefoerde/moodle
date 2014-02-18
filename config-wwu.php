<?php
function selfmsp($clientgiven=false){
    $ssl = true; //$ssl=(strtoupper($_SERVER['HTTPS'])=='ON'); 
    if($clientgiven){
      list($host,$port,$rest)=explode(':',@$_SERVER['HTTP_HOST'].'::',3);
      if($host=='')$host=$_SERVER['SERVER_NAME'];
      if($port=='')$port=$_SERVER['SERVER_PORT'];
    }else{
      $host=$_SERVER['SERVER_NAME']; $port=$_SERVER['SERVER_PORT'];
    }
    $url=($ssl?'https':'http').'://'.$host;
    if($port!=($ssl?443:80))$url.=':'.$port;
    return $url;
}
function selfurl($clientgiven=false){  return selfmsp($clientgiven).$_SERVER['PHP_SELF'];  }

function wwusso_username() { if (array_key_exists( 'REMOTE_USER', $_SERVER) && !empty($_SERVER['REMOTE_USER'])) {return $_SERVER['REMOTE_USER']; } else {return null; } }

function getSSONo() { 
    if( preg_match('/sso([0-9]?).uni-muenster.de/',selfurl(),$match)) return empty($match[1])?0:$match[1];
    else return -1;
}

function getGroupsForZivUser($username) {
    $matches = array();
    $searchthis = "$username ";
    $strlength = strlen($searchthis);

    if(empty($username))
        return $matches;

    $handle = @fopen("/www/data/groups.txt", "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle);
            if(strpos($buffer, $searchthis) === 0) {// only must be first element of line
                $matches[] = substr($buffer, $strlength);
            } elseif(!empty($matches)) {
                break;
        }   }
        fclose($handle);
    }
    return $matches;
}

function initialize_further_configs($debug = false) {
    global $CFG;
    $CFG->mycoursesperpage = 50;
    $CFG->forced_plugin_settings = array();
    
    // These variables define the specific settings for defined course formats.
    // They override any settings defined in the formats own config file.
    //      $CFG->defaultblocks_site = 'site_main_menu,course_list:course_summary,calendar_month';
    //      $CFG->defaultblocks_social = 'participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,course_list';
            $CFG->defaultblocks_topics = 'activity_modules:search_forums,news_items,calendar_upcoming,recent_activity,visual_library';
    //      $CFG->defaultblocks_topics = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
            $CFG->defaultblocks_weeks =  'activity_modules:search_forums,news_items,calendar_upcoming,recent_activity,visual_library';
    //      $CFG->defaultblocks_weeks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
    
    // Completely disable user creation when restoring a course, bypassing any
    // permissions granted via roles and capabilities. Enabling this setting
    // results in the restore process stopping when a user attempts to restore a
    // course requiring users to be created.
    //     $CFG->disableusercreationonrestore = true;
    
    $CFG->emailconnectionerrorsto = 'technik.learnweb@uni-muenster.de';
    
    // Use the following flag to completely disable the Automatic updates deployment
    // feature and hide it from the server administration UI.
    $CFG->disableupdateautodeploy = true;
    $CFG->disableupdatenotifications = true;

    // Use the following flag to completely disable the On-click add-on installation
    // feature and hide it from the server administration UI.
    $CFG->disableonclickaddoninstall = true;

    // Customize essential theme to fit the Learnweb layout
    $CFG->forced_plugin_settings["theme_essential"] = array(
        // Essential: Allgemein
        "oldnavbar" => true,
        "pagewidth" => 1400,
        "layout" => false,
        "breadcrumbstyle" => 3,
        // Essential: Features
        "fitvids" => false,
        "coursecontentsearch" => false,
        "customscrollbars" => false,
        "floatingsubmitbuttons" => false,
        "returntosectionfeature" => false,
        // Essential: Farbe
        "themecolor" => "#006784",
        "themetextcolor" => "#333",
        "themeurlcolor" => "#006784",
        "themehovercolor" => "#666",
        "themeiconcolor" => "#006784",
        "themenavcolor" => "#ffffff",
        "footercolor" => "#8c9598",
        "footertextcolor" => "#fff",
        "footerheadingcolor" => "#fff",
        "footersepcolor" => "#fff",
        "footerurlcolor" => "#fff",
        "footerhovercolor" => "#ededed",
        "themestripebackgroundcolour" => "#ffffff",
        "themestripetextcolour" => "#333333",
        "enablealternativethemecolors1" => false,
        "enablealternativethemecolors2" => false,
        "enablealternativethemecolors3" => false,
        "enablealternativethemecolors4" => false,
        "enablealternativethemecolors5" => false,
        // Essential: Kopfbereich
        "logo" => "hello",
        "headertitle" => 1,
        "navbartitle" => 0,
        "headertextcolor" => "#000000",
        "displaymycourses" => true,
        "mycoursetitle" => "course",
        "helplinktype" => 2,
        "helplink" => "https://sso.uni-muenster.de/LearnWeb/learnweb2/mod/page/view.php?id=148235",
        "displayeditingmenu" => true,
        "hidedefaulteditingbutton" => false,
        // Essential: Zeichensatz
        "fontselect" => 3,
        "fontnameheading" => "MetaWebPro-Normal,sans-serif",
        "fontnamebody" => "MetaWebPro-Normal,sans-serif",
        "copyright" => "",
        "footnote" => "",
        "perfinfo" => "max",
        // Essential: Startseite
        "courselistteachericon" => "graduation-cap",
        "frontpageblocks" => "0",
        // Essential: Analytics
        //"analyticsenabled" => false,
        //"analyticsimagetrack" => false,
        // Essential: Category icons
        "enablecategoryicon" => false,
        // Essential: Slideshow
        "hideonphone" => false,
      );

    if($debug) {
        initialize_further_configs_debug($debug);
    } else {
        @ini_set('display_errors', '0');
        $CFG->debugusers = '2,5,79,14904';
    }
}

function initialize_further_configs_debug($debug = true) {
global $CFG;
// 5. DIRECTORY LOCATION  (most people can just ignore this setting)
$CFG->admin = 'admin';

// Performance profiling
//
//   If you set Debug to "Yes" in the Configuration->Variables page some
//   performance profiling data will show up on your footer (in default theme).
//   With these settings you get more granular control over the capture
//   and printout of the data
//
//   Capture performance profiling data
//   define('MDL_PERF'  , true);
//
//   Capture additional data from DB
//   define('MDL_PERFDB'  , true);
//
//   Print to log (for passive profiling of production servers)
//   define('MDL_PERFTOLOG'  , true);
//
//   Print to footer (works with the default theme)
//   define('MDL_PERFTOFOOT', true);
//
//   Enable earlier profiling that causes more code to be covered
//   on every request (db connections, config load, other inits...).
//   Requires extra configuration to be defined in config.php like:
//   profilingincluded, profilingexcluded, profilingautofrec,
//   profilingallowme, profilingallowall, profilinglifetime
//       $CFG->earlyprofilingenabled = true;
//

// The following setting will turn SQL Error logging on. This will output an
// entry in apache error log indicating the position of the error and the statement
// called. This option will action disregarding error_reporting setting.
//     $CFG->dblogerror = true;
//
// The following setting will log every database query to a table called adodb_logsql.
// Use this setting on a development server only, the table grows quickly!
//     $CFG->logsql = true;
//
// Email database connection errors to someone.  If Moodle cannot connect to the
// database, then email this address with a notice.
     $CFG->emailconnectionerrorsto = 'elearnt@uni-muenster.de';
//
// Set the priority of themes from highest to lowest. This is useful (for
// example) in sites where the user theme should override all other theme
// settings for accessibility reasons. You can also disable types of themes
// (other than site)  by removing them from the array. The default setting is:
//      $CFG->themeorder = array('course', 'category', 'session', 'user', 'site');
// NOTE: course, category, session, user themes still require the
// respective settings to be enabled
//
// It is possible to add extra themes directory stored outside of $CFG->dirroot.
// This local directory does not have to be accessible from internet.
//
//     $CFG->themedir = '/location/of/extra/themes';
//
// It is possible to specify different cache and temp directories, use local fast filesystem
// for normal web servers. Server clusters MUST use shared filesystem for cachedir!
// Localcachedir is intended for server clusters, it does not have to be shared by cluster nodes.
// The directories must not be accessible via web.
//
//     $CFG->tempdir = '/var/www/moodle/temp';        // Files used during one HTTP request only.
//     $CFG->cachedir = '/var/www/moodle/cache';      // Directory MUST BE SHARED by all cluster nodes, locking required.
//     $CFG->localcachedir = '/var/local/cache';      // Intended for local node caching.
//
// Some filesystems such as NFS may not support file locking operations.
// Locking resolves race conditions and is strongly recommended for production servers.
//     $CFG->preventfilelocking = false;
//
// Site default language can be set via standard administration interface. If you
// want to have initial error messages for eventual database connection problems
// localized too, you have to set your language code here.
//
//     $CFG->lang = 'yourlangcode'; // for example 'cs'
//
// When Moodle is about to perform an intensive operation it raises PHP's memory
// limit. The following setting should be used on large sites to set the raised
// memory limit to something higher.
// The value for the settings should be a valid PHP memory value. e.g. 512M, 1G
//
//     $CFG->extramemorylimit = '1024M';
//
// Moodle 2.4 introduced a new cache API.
// The cache API stores a configuration file within the Moodle data directory and
// uses that rather than the database in order to function in a stand-alone manner.
// Using altcacheconfigpath you can change the location where this config file is
// looked for.
// It can either be a directory in which to store the file, or the full path to the
// file if you want to take full control. Either way it must be writable by the
// webserver.
//
//     $CFG->altcacheconfigpath = '/var/www/shared/moodle.cache.config.php
//


$CFG->enablecssoptimiser = true;
// If set the CSS optimiser will add stats about the optimisation to the top of the optimised CSS file.
$CFG->cssoptimiserstats = true;
// If set the CSS that is optimised will still retain a minimalistic formatting so that anyone wanting to can still clearly read it.
//      $CFG->cssoptimiserpretty = true;
//
// Use the following flag to completely disable the Automatic updates deployment
// feature and hide it from the server administration UI.
//
//      $CFG->disableupdateautodeploy = true;
//
// Use the following flag to completely disable the On-click add-on installation
// feature and hide it from the server administration UI.
//
$CFG->disableonclickaddoninstall = true;
//
// As of version 2.4 Moodle serves icons as SVG images if the users browser appears to support SVG.
// To ensure they are always used when available:
//      $CFG->svgicons = true;
// To ensure they are never used even when available:
//      $CFG->svgicons = false;
//
// Some administration options allow setting the path to executable files. This can
// potentially cause a security risk. Set this option to true to disable editing
// those config settings via the web. They will need to be set explicitly in the
// config.php file
//      $CFG->preventexecpath = true;
//
// Use the following flag to set userid for noreply user. If not set then moodle will
// create dummy user and use -ve value as user id.
//      $CFG->noreplyuserid = -10;
//
// As of version 2.6 Moodle supports admin to set support user. If not set, all mails
// will be sent to supportemail.
//      $CFG->supportuserid = -20;
//
//=========================================================================
// 7. SETTINGS FOR DEVELOPMENT SERVERS - not intended for production use!!!
//=========================================================================
//
// Force a debugging mode regardless the settings in the site administration
@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
@ini_set('display_errors', '1');    // NOT FOR PRODUCTION SERVERS!
$CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
$CFG->debugdisplay = 1;             // NOT FOR PRODUCTION SERVERS!
//
// You can specify a comma separated list of user ids that that always see
// debug messages, this overrides the debug flag in $CFG->debug and $CFG->debugdisplay
// for these users only.
// $CFG->debugusers = '2,3,5';
//
// Prevent theme caching
 //$CFG->themedesignermode = true; // NOT FOR PRODUCTION SERVERS!
//
// Prevent JS caching
// $CFG->cachejs = false; // NOT FOR PRODUCTION SERVERS!
//
// Restrict which YUI logging statements are shown in the browser console.
// For details see the upstream documentation:
//   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logInclude
//   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logExclude
// $CFG->yuiloginclude = array(
//     'moodle-core-dock-loader' => true,
//     'moodle-course-categoryexpander' => true,
// );
// $CFG->yuilogexclude = array(
//     'moodle-core-dock' => true,
//     'moodle-core-notification' => true,
// );
//
// Set the minimum log level for YUI logging statements.
// For details see the upstream documentation:
//   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logLevel
// $CFG->yuiloglevel = 'debug';
//
// Prevent core_string_manager application caching
// $CFG->langstringcache = false; // NOT FOR PRODUCTION SERVERS!
//
// When working with production data on test servers, no emails or other messages
// should ever be send to real users
// $CFG->noemailever = true;    // NOT FOR PRODUCTION SERVERS!
//
// Divert all outgoing emails to this address to test and debug emailing features
$CFG->divertallemailsto = 'elearnt@uni-muenster.de'; // NOT FOR PRODUCTION SERVERS!
//
// Uncomment if you want to allow empty comments when modifying install.xml files.
// $CFG->xmldbdisablecommentchecking = true;    // NOT FOR PRODUCTION SERVERS!
//
// Since 2.0 sql queries are not shown during upgrade by default.
// Please note that this setting may produce very long upgrade page on large sites.
// $CFG->upgradeshowsql = true; // NOT FOR PRODUCTION SERVERS!
//
// Add SQL queries to the output of cron, just before their execution
// $CFG->showcronsql = true;
//
// Force developer level debug and add debug info to the output of cron
// $CFG->showcrondebugging = true;
//
//=========================================================================
// 8. FORCED SETTINGS
//=========================================================================
// It is possible to specify normal admin settings here, the point is that
// they can not be changed through the standard admin settings pages any more.
//
// Core settings are specified directly via assignment to $CFG variable.
// Example:
//   $CFG->somecoresetting = 'value';
     $CFG->supportname = 'Administrator '.$CFG->dbname;
     $CFG->auth = 'limitedaccess,sso,ldap';
     $CFG->guestloginbutton = false;
//
// Plugin settings have to be put into a special array.
// Example:
//   $CFG->forced_plugin_settings = array('pluginname'  => array('settingname' => 'value', 'secondsetting' => 'othervalue'),
//                                        'otherplugin' => array('mysetting' => 'myvalue', 'thesetting' => 'thevalue'));
// Module default settings with advanced/locked checkboxes can be set too. To do this, add
// an extra config with '_adv' or '_locked' as a suffix and set the value to true or false.
// Example:
//   $CFG->forced_plugin_settings = array('pluginname'  => array('settingname' => 'value', 'settingname_locked' => true, 'settingname_adv' => true));
    $CFG->forced_plugin_settings['backup'] = array('backup_auto_active' => '0', 'backup_auto_destination' => '/www/data/LearnWebTest/scratch/test');
    $CFG->forced_plugin_settings['local_lsf_unification'] = array('max_import_age' => '1024');

//
//=========================================================================
// 9. PHPUNIT SUPPORT
//=========================================================================
// $CFG->phpunit_prefix = 'phpu_';
// $CFG->phpunit_dataroot = '/home/example/phpu_moodledata';
// $CFG->phpunit_directorypermissions = 02777; // optional
//
//
//=========================================================================
// 10. SECRET PASSWORD SALT
//=========================================================================
// A single site-wide password salt is no longer required *unless* you are
// upgrading an older version of Moodle (prior to 2.5), or if you are using
// a PHP version below 5.3.7. If upgrading, keep any values from your old
// config.php file. If you are using PHP < 5.3.7 set to a long random string
// below:
//
// $CFG->passwordsaltmain = 'a_very_long_random_string_of_characters#@6&*1';
//
// You may also have some alternative salts to allow migration from previously
// used salts.
//
// $CFG->passwordsaltalt1 = '';
// $CFG->passwordsaltalt2 = '';
// $CFG->passwordsaltalt3 = '';
// ....
// $CFG->passwordsaltalt19 = '';
// $CFG->passwordsaltalt20 = '';
//
//
//=========================================================================
// 11. BEHAT SUPPORT
//=========================================================================
// Behat needs a separate data directory and unique database prefix:
//
// $CFG->behat_prefix = 'bht_';
// $CFG->behat_dataroot = '/home/example/bht_moodledata';
//
// To set a seperate wwwroot for Behat to use, use $CFG->behat_wwwroot; this is set automatically
// to http://localhost:8000 as it is the proposed PHP built-in server URL. Instead of that you can,
// for example, use an alias, add a host to /etc/hosts or add a new virtual host having a URL
// poiting to your production site and another one poiting to your test site. Note that you need
// to ensure that this URL is not accessible from the www as the behat test site uses "sugar"
// credentials (admin/admin) and can be easily hackable.
//
// Example:
//   $CFG->behat_wwwroot = 'http://192.168.1.250:8000';
//   $CFG->behat_wwwroot = 'http://localhost/moodlesitetesting';
//
// You can override default Moodle configuration for Behat and add your own
// params; here you can add more profiles, use different Mink drivers than Selenium...
// These params would be merged with the default Moodle behat.yml, giving priority
// to the ones specified here. The array format is YAML, following the Behat
// params hierarchy. More info: http://docs.behat.org/guides/7.config.html
// Example:
//   $CFG->behat_config = array(
//       'default' => array(
//           'formatter' => array(
//               'name' => 'pretty',
//               'parameters' => array(
//                   'decorated' => true,
//                   'verbose' => false
//               )
//           )
//       ),
//       'Mac-Firefox' => array(
//           'extensions' => array(
//               'Behat\MinkExtension\Extension' => array(
//                   'selenium2' => array(
//                       'browser' => 'firefox',
//                       'capabilities' => array(
//                           'platform' => 'OS X 10.6',
//                           'version' => 20
//                       )
//                   )
//               )
//           )
//       ),
//       'Mac-Safari' => array(
//           'extensions' => array(
//               'Behat\MinkExtension\Extension' => array(
//                   'selenium2' => array(
//                       'browser' => 'safari',
//                       'capabilities' => array(
//                           'platform' => 'OS X 10.8',
//                           'version' => 6
//                       )
//                   )
//               )
//           )
//       )
//   );
//
// You can completely switch to test environment when "php admin/tool/behat/cli/util --enable",
// this means that all the site accesses will be routed to the test environment instead of
// the regular one, so NEVER USE THIS SETTING IN PRODUCTION SITES. This setting is useful
// when working with cloud CI (continous integration) servers which requires public sites to run the
// tests, or in testing/development installations when you are developing in a pre-PHP 5.4 server.
// Note that with this setting enabled $CFG->behat_wwwroot is ignored and $CFG->behat_wwwroot
// value will be the regular $CFG->wwwroot value.
// Example:
//   $CFG->behat_switchcompletely = true;
//
// You can force the browser session (not user's sessions) to restart after N seconds. This could
// be useful if you are using a cloud-based service with time restrictions in the browser side.
// Setting this value the browser session that Behat is using will be restarted. Set the time in
// seconds. Is not recommended to use this setting if you don't explicitly need it.
// Example:
//   $CFG->behat_restart_browser_after = 7200;     // Restarts the browser session after 2 hours
//
// All this page's extra Moodle settings are compared against a white list of allowed settings
// (the basic and behat_* ones) to avoid problems with production environments. This setting can be
// used to expand the default white list with an array of extra settings.
// Example:
//   $CFG->behat_extraallowedsettings = array('logsql', 'dblogerror');
//
// You can make behat save several dumps when a scenario fails. The dumps currently saved are:
// * a dump of the DOM in it's state at the time of failure; and
// * a screenshot (JavaScript is required for the screenshot functionality, so not all browsers support this option)
// Example:
//   $CFG->behat_faildump_path = '/my/path/to/save/failure/dumps';
//
//=========================================================================
// 12. DEVELOPER DATA GENERATOR
//=========================================================================
//
// The developer data generator tool is intended to be used only in development or testing sites and
// it's usage in production environments is not recommended; if it is used to create JMeter test plans
// is even less recommended as JMeter needs to log in as site course users. JMeter needs to know the
// users passwords but would be dangerous to have a default password as everybody would know it, which would
// be specially dangerouse if somebody uses this tool in a production site, so in order to prevent unintended
// uses of the tool and undesired accesses as well, is compulsory to set a password for the users
// generated by this tool, but only in case you want to generate a JMeter test. The value should be a string.
// Example:
//   $CFG->tool_generator_users_password = 'examplepassword';

//=========================================================================
// ALL DONE!  To continue installation, visit your main page with a browser
//=========================================================================


$CFG->alternativeupdateproviderurl = 'http://download.moodle.org/api/1.2/updates.php';

}
?>
