=== Codex Creator ===
Contributors: stiofansisland, paoltaia
Tags: Codex, Codex Creator, Documentation, Documentation Generator
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=payments@nomaddevs.com&item_name=Donation+for+Codex+Creator
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html


Below is the DocBlock standards and cheat sheet that should be used.

please see this page for WP standards: https://make.wordpress.org/core/handbook/inline-documentation-standards/php-documentation-standards/

@global type $varname Description.
type can be any of the following: bool || float || int || array || object || wpdb
$varname is the variable that is declared global.
Description is the description text and should end with a period (.)

global cheat sheet:
@global wpdb $wpdb WordPress database abstraction object.
@global object $current_user The current user object which holds the user data.
@global WP_Post|null $post The current post, if available.
@global bool $preview True if the current page is add listing preview page. False if not.


ACTIONS/FILTERS
when writing a DocBlock for an action or filter the package name is not required.
when the action/filter is inside a functions please use the see tag like this: @see my_awesome_functions_name function.
If there are other filter or actions also in the containing functions please list theme with the see tag also: @see my_awesome_action_name action.