<?php

/*
Plugin Name: Multisite post scheduler
Description: Plugin to schedule large number of posts.
Version: 1.0
Author: Nikola Pavlicevic
Author URI: http://npavlicevic.wordpress.com
Tags: posts, schedule, publish
Licence: GPL2
*/

add_action("admin_menu","psch_admin_menu");
add_action("admin_init","psch_register_options");
add_action("init","psch_enq_scripts");
add_action("admin_head","psch_print_scripts");

function psch_admin_menu(){
	add_submenu_page("options-general.php","Post scheduler","Post scheduler","administrator","psch-options","psch_options_page");
}

function psch_register_options(){
	register_setting("psch_settings","psch_title");
	register_setting("psch_settings","psch_content");
	register_setting("psch_settings","psch_day");
	register_setting("psch_settings","psch_month");
	register_setting("psch_settings","psch_year");
	register_setting("psch_settings","psch_hour");
	register_setting("psch_settings","psch_minute");
	register_setting("psch_settings","psch_blog");
	register_setting("psch_settings","psch_user");
	register_setting("psch_settings","psch_tags");
	register_setting("psch_settings","psch_cat");
}

function psch_options_page(){
	//print_r($_GET);

	if($_GET["settings-updated"]==true)
		psch_save_posts();
	
	psch_print_form();
	
	/*print_r(get_option("psch_title"));
	echo "<br/>";
	print_r(get_option("psch_content"));
	echo "<br/>";
	print_r(get_option("psch_day"));
	echo "<br/>";
	print_r(get_option("psch_month"));
	echo "<br/>";
	print_r(get_option("psch_year"));
	echo "<br/>";
	print_r(get_option("psch_hour"));
	echo "<br/>";
	print_r(get_option("psch_minute"));
	echo "<br/>";
	print_r(get_option("psch_user"));
	echo "<br/>";
	print_r(get_option("psch_tags"));
	echo "<br/>";
	print_r(get_option("psch_cat"));
	echo "<br/>";
	echo get_option("gmt_offset");
	echo "<br/>";
	print_r(get_option("psch_blog"));*/
}

function psch_enq_scripts(){
	wp_enqueue_scripts("jquery");
}

function psch_print_scripts(){
	?>
	<script type="text/javascript">
		
		psch_entry_count=1;
	
		jQuery(document).ready(psch_main_func);
		
		function psch_main_func(){
			//alert("main func");
			psch_add_click_psch_add_post();
			psch_add_click_psch_remove_post();
			psch_add_change_blog();
		}
		
		function psch_add_click_psch_add_post(){
			jQuery("a.psch_add_post").live("click",function(){
				psch_entry_count+=1;
				//alert(psch_entry_count);
				psch_new_entry=jQuery("#psch_append_table").clone();
				jQuery(psch_new_entry).removeAttr("id");
				//alert(jQuery(psch_new_entry).html());
				//alert(jQuery(psch_new_entry).html().text());
				jQuery(psch_new_entry).show();
				//alert(jQuery(psch_new_entry).html());
				jQuery("#psch_main_table tbody:first").append("<tr id='psch_entry_"+psch_entry_count+"'><td></td></tr>");
				jQuery("#psch_entry_"+psch_entry_count+" td").html(psch_new_entry);
				//alert("add post clicked");
				return false;
			});
		}
		
		function psch_add_click_psch_remove_post(){
			jQuery("a.psch_remove_post").live("click",function(){
				//alert(jQuery("#psch_main_table").html());
				parent_row=jQuery(this).parents("table").parents("tr");
				id=jQuery(parent_row).attr("id");
				jQuery("#"+id).remove();
				//alert(jQuery("#psch_main_table").html());
				return false;
			});
		}
		
		function psch_add_change_blog(){
			jQuery("select.psch_blog_select").live("change",function(){
				parent=jQuery(this).parents("tbody:first");
				val=jQuery(this).val();
				select_auth=jQuery("#psch_select_holder select.psch_author_"+val).clone();
				select_cat=jQuery("#psch_select_holder select.psch_cat_"+val).clone();
				jQuery(parent).children("tr").children("td").children("div.psch_user_holder").children("select.psch_author").remove();
				jQuery(parent).children("tr").children("td").children("div.psch_user_holder").append(select_auth);
				jQuery(parent).children("tr").children("td").children("div.psch_cat_holder").children("select.psch_cat").remove();
				jQuery(parent).children("tr").children("td").children("div.psch_cat_holder").append(select_cat);
			});
		}
				
	</script>
	<?php
}

function psch_print_form(){
	global $wpdb;
	?>
	<div class="wrap">
	<h2>Add posts</h2>
	<form method="post" action="options.php">
		<?php settings_fields("psch_settings");?>
		<p class="submit">
			<input type="submit" class="button-primary" value="Insert posts" id="psch_submit"/>
		</p>
		<table class="form-table" id="psch_main_table">
			<tr id="psch_entry_1"><td>
				<table class="psch_row_form" width="95%">
					<tr><td><label for="psch_title" name="psch_title_label">Post title</label></td></tr>
					<tr><td><input type="text" name="psch_title[]" style="width:95%;" value="" /></td></tr>
					<tr><td><label for="psch_content" name="psch_content_label">Post content</label></td></tr>
					<tr><td><textarea name="psch_content[]" style="width:95%;" rows="25" cols="50"></textarea></td></tr>
					<tr><td><label>Publish date</label></td></tr>
					<tr><td><label for="psch_day">Day: </label><input type="text" name="psch_day[]" value="" />&nbsp;<label for="psch_month">Month:</label><input type="text" name="psch_month[]" value=""/>&nbsp;<label for="psch_year">Year:</label><input type="text" name="psch_year[]" value=""/>&nbsp;<label for="psch_hour">Hours:</label><input type="text" name="psch_hour[]" value=""/>&nbsp;<label for="psch_minute">Minutes: </label><input type="text" name="psch_minute[]" value=""/></td></tr>
					<tr><td><i>(Day:number between 1 and number of days in the month, Month:number between 1 and 12, Year:any non-negative number, Hours:number between 0 and 23, Minutes:number betweeen 0 and 59.)</i></td></tr>
					<tr><td><label for="psch_blog">Blog</label></td></tr>
					<tr><td>
						<select name="psch_blog[]" class="psch_blog_select">
						<?php
							$query="select * from {$wpdb->blogs}";
							$blogs=$wpdb->get_results($query,ARRAY_A);
							$list_items="";
							$n=count($blogs);
							
							for($i=0;$i<$n;$i++){
								$blog_dets=get_blog_details($blogs[$i]['blog_id']);
								
								if($i==0)
									$list_items.="<option selected='yes' value='{$blog_dets->blog_id}'>{$blog_dets->blogname}</option>";
								else
									$list_items.="<option value='{$blog_dets->blog_id}'>{$blog_dets->blogname}</option>";
							}
							
							echo $list_items;
						?>
						</select>
					</td></tr>
					<tr><td><label for="psch_user">Author</label></td></tr>
					<tr><td>
						<div class="psch_user_holder"></div>
					</td></tr>
					<tr><td><label for="psch_cat">Category</label></td></tr>
					<tr><td>
						<div class="psch_cat_holder"></div>
					</td></tr>
					<tr><td><label for="psch_tags">Post tags (comma separated)</label></td></tr>
					<tr><td><input type="text" name="psch_tags[]" style="width:95%;" value="" /></td></tr>
					<tr><td align="right"><a href="#" class="psch_add_post">Add post</a>&nbsp;<a href="#" class="psch_remove_post">Remove post</a>&nbsp;<a href="#" class="psch_page_top">Back to page top</a></td></tr>
					<tr><td><hr /></td></tr>
				</table>
			</td></tr>
		</table>
	</form>
	</div>
	
	<table id="psch_append_table" class="psch_row_form" width="95%" style="display:none;">
		<tr><td><label for="psch_title" name="psch_title_label">Post title</label></td></tr>
		<tr><td><input type="text" name="psch_title[]" style="width:95%;" value="" /></td></tr>
		<tr><td><label for="psch_content" name="psch_content_label">Post content</label></td></tr>
		<tr><td><textarea name="psch_content[]" style="width:95%;" rows="25" cols="50"></textarea></td></tr>
		<tr><td><label>Publish date</label></td></tr>
		<tr><td><label for="psch_day">Day: </label><input type="text" name="psch_day[]" value="" />&nbsp;<label for="psch_month">Month:</label><input type="text" name="psch_month[]" value=""/>&nbsp;<label for="psch_year">Year:</label><input type="text" name="psch_year[]" value=""/>&nbsp;<label for="psch_hour">Hours:</label><input type="text" name="psch_hour[]" value=""/>&nbsp;<label for="psch_minute">Minutes: </label><input type="text" name="psch_minute[]" value=""/></td></tr>
		<tr><td><i>(Day:number between 1 and number of days in the month, Month:number between 1 and 12, Year:any non-negative number, Hours:number between 0 and 23, Minutes:number betweeen 0 and 59.)</i></td></tr>
		<tr><td><label for="psch_blog">Blog</label></td></tr>
		<tr><td>
				<select name="psch_blog[]" class="psch_blog_select">
				<?php
					$query="select * from {$wpdb->blogs}";
					$blogs=$wpdb->get_results($query,ARRAY_A);
					$list_items="";
					$n=count($blogs);
							
					for($i=0;$i<$n;$i++){
						$blog_dets=get_blog_details($blogs[$i]['blog_id']);
								
						if($i==0)
							$list_items.="<option selected='yes' value='{$blog_dets->blog_id}'>{$blog_dets->blogname}</option>";
						else
							$list_items.="<option value='{$blog_dets->blog_id}'>{$blog_dets->blogname}</option>";
						}
							
						echo $list_items;
				?>
				</select>
		</td></tr>
		<tr><td><label for="user">Author:</label></td></tr>
		<tr><td>
			<div class="psch_user_holder"></div>
		</td></tr>
		<tr><td><label for="psch_cat">Category</label></td></tr>
		<tr><td>
			<div class="psch_cat_holder"></div>
		</td></tr>
		<tr><td><label for="psch_tags">Post tags (comma separated)</label></td></tr>
		<tr><td><input type="text" name="psch_tags[]" style="width:95%;" value="" /></td></tr>
		<tr><td align="right"><a href="#" class="psch_add_post">Add post</a>&nbsp;<a href="#" class="psch_remove_post">Remove post</a>&nbsp;<a href="#" class="psch_page_top">Back to page top</a></td></tr>
		<tr><td><hr /></td></tr>
	</table>
	
	<div id="psch_select_holder" style="display:none;">
		<?php
			$authors_list="";
			$cat_list="";
			
			foreach($blogs as $blog){
				switch_to_blog($blog["blog_id"]);
				
				$args=array("orderby"=>"login");
				$users=get_users($args);
				
				$args=array("orderby"=>"name","hide_empty"=>0);
				$cats=get_categories($args);
				
				$authors_list.="<select name='psch_user[]' class='psch_author_{$blog['blog_id']} psch_author'>";
				$cat_list.="<select name='psch_cat[]' class='psch_cat_{$blog['blog_id']} psch_cat'>";
				
				foreach($users as $user){
					$authors_list.="<option>{$user->user_login}</option>";
				}
				
				foreach($cats as $cat){
					$cat_list.="<option>{$cat->name}</option>";
				}
				
				$authors_list.="</select>";
				$cat_list.="</select>";
			}
			
			restore_current_blog();
			
			echo $authors_list;
			echo $cat_list;
			
		?>
	</div>
	<?php
}

function psch_save_posts(){
	$psch_title=get_option("psch_title");
	$psch_content=get_option("psch_content");
	$psch_day=get_option("psch_day");
	$psch_month=get_option("psch_month");
	$psch_year=get_option("psch_year");
	$psch_hour=get_option("psch_hour");
	$psch_minute=get_option("psch_minute");
	$psch_user=get_option("psch_user");
	$psch_tags=get_option("psch_tags");
	$psch_cat=get_option("psch_cat");
	$psch_blog=get_option("psch_blog");
	
	$n=count($psch_title);
	
	for($i=0;$i<$n;$i++){
		switch_to_blog($psch_blog[$i]);
		$post_category_arr=term_exists($psch_cat[$i],"category");
		$post_category=$post_category_arr["term_id"];
		$post_author=get_userdatabylogin($psch_user[$i])->ID;
		$post_date=$psch_year[$i]."-".$psch_month[$i]."-".$psch_day[$i]." ".$psch_hour[$i].":".$psch_minute[$i].":".date("s");
		$time_diff=get_option("gmt_offset");
		$post_date_gmt=date("Y-m-d H:i:s",strtotime($post_date)-($time_diff*3600));
		unset($args);
		$args=array(
			"post_title"=>$psch_title[$i],
			"post_content"=>$psch_content[$i],
			"post_status"=>"publish",
			"post_author"=>$post_author,
			"post_category"=>array($post_category),
			"post_date"=>$post_date,
			"post_date_gmt"=>$post_date_gmt
		);
		
		$post_id=wp_insert_post($args);
		$post_tags=explode(",",$psch_tags[$i]);
		wp_set_post_terms($post_id,$post_tags);
		restore_current_blog();
	}
}
	
?>