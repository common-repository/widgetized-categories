<?php
/*
Plugin Name: Widgetized Categories
Plugin URI: http://wordpress.org/extend/plugins/widetized-categories/
Description: This plugin allows you to exclude certain categories from showing on your WordPress sidebar or to display single category and it's subcategories.
Version: 1.0
Author: InselPark
Author URI: http://www.inselpark.com
License: GPL2

Copyright 2012  InselPark  (email : office@inselpark.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Adds Widgetized_Categories widget.
 */
class Widgetized_Categories extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'widgetized_categories', // Base ID
			'Widgetized Categories', // Name
			array( 'description' => __( "Add categories to your sidebar and exclude the ones you don't want to display in the list.", 'text_domain' ), ) // Args
		);
	}
	

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		
 		echo '<ul id="widgetized_categories_widget">';
			$cat_exc = trim($instance['widgetized_categories_excluded']);
			$cat_inc = trim($instance['widgetized_categories_single']);
			$post_count = $instance['widgetized_categories_post_count'];
			if($post_count == 'Yes'){ $show_count = 1; }else{ $show_count = 0; }
			
			if($cat_inc){
				$category = get_category($cat_inc);
				$category_url = get_category_link($cat_inc);
			}
			
			$category_params = array(
					'echo' => 0,
					'hide_empty' => 0,
					'title_li'   => '',
					'show_count' => $show_count,
					'show_option_none'   => __('')
			);
			
			if( strlen($cat_inc) > 0 and $cat_inc != 0 and strlen($category_url)>0):
				$category_params['child_of'] = $cat_inc;
				
			?>
				<li class="cat-item cat-item-<?php echo $cat_inc; ?>">
				<a title="View all posts filed under <?php echo $category->name; ?>" href="<?php echo $category_url; ?>"><?php echo $category->name; ?></a><?php if($post_count == 'Yes'): ?> <span class="post_count">(<?php echo $category->count; ?>)</span> <?php endif; ?>
					<ul class="children">
						<?php 
							$list_build = wp_list_categories($category_params); 
							$list_build = str_replace('(', '<span class="post_count">(', $list_build);
							$list_build = str_replace(')', ')</span>', $list_build);
							echo $list_build;
						?>
					</ul>
				</li>
			<?php
				
			elseif(strlen($cat_exc) > 0):
				$category_params['exclude'] = $cat_exc;
				$list_build = wp_list_categories($category_params); 
				$list_build = str_replace('(', '<span class="post_count">(', $list_build);
				$list_build = str_replace(')', ')</span>', $list_build);
				echo $list_build;
			else:
				$list_build = wp_list_categories($category_params); 
				$list_build = str_replace('(', '<span class="post_count">(', $list_build);
				$list_build = str_replace(')', ')</span>', $list_build);
				echo $list_build;
			endif;
			
		echo '</ul>';		
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['widgetized_categories_excluded'] = strip_tags( $new_instance['widgetized_categories_excluded'] );
		$instance['widgetized_categories_single'] = strip_tags( $new_instance['widgetized_categories_single'] );
		$instance['widgetized_categories_post_count'] = $new_instance['widgetized_categories_post_count'];
		

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Widgetized Categories', 'text_domain' );
		}
		if ( isset( $instance[ 'widgetized_categories_excluded' ] ) ) { 
			$widgetized_categories_excluded = $instance[ 'widgetized_categories_excluded' ]; 
		}
		if ( isset( $instance[ 'widgetized_categories_single' ] ) ) { 
			$widgetized_categories_single = $instance[ 'widgetized_categories_single' ]; 
		}
		if ( isset( $instance['widgetized_categories_post_count'] ) ) {
			$post_count_selected = $instance['widgetized_categories_post_count'];
		}
		else {
			$post_count_selected = 'Yes';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <span>
		<label for="<?php echo $this->get_field_id( 'widgetized_categories_excluded' ); ?>"><?php _e( 'Categories To Exclude:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'widgetized_categories_excluded' ); ?>" name="<?php echo $this->get_field_name( 'widgetized_categories_excluded' ); ?>" type="text" value="<?php echo esc_attr( $widgetized_categories_excluded ); ?>" />
		</span>
        <p>Enter a comma-seperated list of category ID numbers, e.g. <code>3,8,10.</code> (This widget will display all of your categories except these category ID numbers).</p>
        <span>
		<label for="<?php echo $this->get_field_id( 'widgetized_categories_single' ); ?>"><?php _e( 'Single Category To Display:' ); ?></label> 
		<input class="widefat" maxlength="3" id="<?php echo $this->get_field_id( 'widgetized_categories_single' ); ?>" name="<?php echo $this->get_field_name( 'widgetized_categories_single' ); ?>" type="text" value="<?php echo esc_attr( $widgetized_categories_single ); ?>" />
		</span>
        <p>Enter single ID of the category to be displayed together with it's subcategories). This parameter overrides the exclude parameter.</p>
        <label for="<?php echo $this->get_field_id( 'widgetized_categories_post_count' ); ?>"><?php _e( 'Display Post Count:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'widgetized_categories_post_count' ); ?>" name="<?php echo $this->get_field_name( 'widgetized_categories_post_count' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'Yes' == $post_count_selected ) echo 'selected="selected"'; ?>>Yes</option>
				<option <?php if ( 'No' == $post_count_selected ) echo 'selected="selected"'; ?>>No</option>
			</select>
		</span>
        <p>Choose if you want to display post count next to the category name.</p>
		<?php 
	}

} // class Foo_Widget

add_action( 'widgets_init', function(){
     return register_widget( 'Widgetized_Categories' );
});

?>