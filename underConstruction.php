<?php
/*
 Plugin Name: Under Construction
 Plugin URI: http://www.masseltech.com/
 Description: Makes it so your site can only be accessed by users who log in. Useful for developing a site on a live server, without the world being able to see it
 Version: 1.0
 Author: Jeremy Massel
 Author URI: http://www.masseltech.com/
 */

/*
 This file is part of underConstruction.

    underConstruction is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    underConstruction is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with underConstruction.  If not, see <http://www.gnu.org/licenses/>.
 */

?>
<?php

add_action('get_header', 'uc_overrideWP');
add_action('admin_menu', 'uc_adminMenu');
register_deactivation_hook(__FILE__, 'uc_remove');

function uc_overrideWP()
{
    if (!is_user_logged_in())
    {
        if (get_option('underConstructionHTML'))
        {
            echo ucGetHTML();
			die();
        }
        else
        {
            require_once ('defaultMessage.php');
			die();
        }
    }
}

function uc_adminMenu()
{
    add_options_page('Under Construction Message', 'Under Construction', 8, basename(__FILE__) , 'uc_changeMessage');
}

function uc_changeMessage()
{

	if(isset($_POST['ucHTML'])){
		if(trim($_POST['ucHTML'])){
			update_option('underConstructionHTML', attribute_escape($_POST['ucHTML']));
		}
	}
	
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32">
        <br/>
    </div>
    <h2>Under Construction</h2>
    <form method="post" action="<?php echo $GLOBALS['PHP_SELF'] . '?page=underConstruction.php'; ?>">
        <h3>Under Construction Page HTML</h3>
		<p>Put in this area the HTML you want to show up on your front page</p>
        <textarea name="ucHTML" rows="15" cols="75"><?php if(get_option('underConstructionHTML')){echo ucGetHTML();}?></textarea>
     
		<p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="Save Changes" id="submitCalendarAdd"/>
    	</p>
	</form>
</div>
<?php
}

function ucGetHTML()
{
	return stripslashes(html_entity_decode(get_option('underConstructionHTML')));
}

function ucRemove(){
	delete_option('underConstructionHTML');
}
?>
