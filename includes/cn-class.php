<?php
/**
 * Adds ChowNow widget.
 */
class ChowNow_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'chownow_widget', // Base ID
			esc_html__( 'ChowNow Integration', 'cn_domain' ), // Name
			array( 'description' => esc_html__( 'Widget to integrate ChowNow Ordering', 'cn_domain' ), ) // Args
		);
		add_action( 'wp_enqueue_scripts', $this);
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

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// enqueue css and add inline css
		$cn_css = '
		@media (max-width: 767px) {
			.cn-cta-mobile-container{
				background: '.$instance['color'].';
			}
			.cn-cta-mobile-btn {
				color: '.$instance['color'].' !important; 
			}
		}
		';
		wp_register_style( 'cnintegration-style', plugins_url( 'css/style.css' , __FILE__ ) );
		wp_enqueue_style( 'cnintegration-style' );
		wp_add_inline_style( 'cnintegration-style', $cn_css );

		// enqueue js
		wp_enqueue_script( 'cnintegration', 'https://cf.chownowcdn.com/latest/static/integrations/ordering-modal.min.js', array(), '1', true );

		// include data attribute for cn integration script via anonymous function to pass down the cn_companyid param into a filter
		$data_attr_func = function( $tag, $handle ) use ( $instance ) 
		{
			if ( 'cnintegration' !== $handle ) {
					return $tag;
			}
			$tag = str_replace( 'src=', 'data-chownow-company-id="'.$instance['companyId'].'" src=', $tag );
			return $tag;
		};
		add_filter( 'script_loader_tag', $data_attr_func, 10, 3);
	
    if($instance['display'] == 'yes') {
			echo '
					<div class="cn-cta-mobile-container">
						<a class="chownow-order-online cn-cta-mobile-btn" href="https://ordering.chownow.com/order/'.$instance['companyId'].'/locations" target="_blank">Order Online</a>
					</div>
      ';
		}
		
		echo $args['after_widget'];

	}

	public function enqueueAssets( $instance ) {
		wp_enqueue_script( 'cnintegration', 'https://cf.chownowcdn.com/latest/static/integrations/ordering-modal.min.js', array(), '1', true );
		
  }


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$companyId = ! empty( $instance['companyId'] ) ? $instance['companyId'] : esc_html__( '1', 'cn_domain' );
    $display = ! empty( $instance['display'] ) ? $instance['display'] : esc_html__( 'yes', 'cn_domain' );
    $color = ! empty( $instance['color'] ) ? $instance['color'] : esc_html__( '#dc143c', 'cn_domain' );
		?>

    <!-- Company Id -->
		<p>
  		<label for="<?php echo esc_attr( $this->get_field_id( 'companyId' ) ); ?>">
        <?php esc_attr_e( 'Company ID:', 'cn_domain' ); ?>
      </label>

  		<input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'companyId' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'companyId' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $companyId ); ?>">
		</p>

    <!-- Display Mobile Anchor -->
		<p>
  		<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>">
        <?php esc_attr_e( 'Display Mobile Anchor:', 'cn_domain' ); ?>
      </label>

  		<select
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
        <option value="yes" <?php echo ($display == 'yes') ? 'selected' : ''; ?>>Yes</option>
        <option value="no" <?php echo ($display == 'no') ? 'selected' : ''; ?>>No</option>
      </select>
		</p>

    <!-- Mobile Button Color -->
		<p>
  		<label for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>">
        <?php esc_attr_e( 'Mobile Button Color:', 'cn_domain' ); ?>
      </label>

  		<input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $color ); ?>">
		</p>
		<?php
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
		$instance['companyId'] = ( ! empty( $new_instance['companyId'] ) ) ? sanitize_text_field( $new_instance['companyId'] ) : '';
    $instance['display'] = ( ! empty( $new_instance['display'] ) ) ? sanitize_text_field( $new_instance['display'] ) : '';
    $instance['color'] = ( ! empty( $new_instance['color'] ) ) ? sanitize_text_field( $new_instance['color'] ) : '';

		return $instance;
	}

} // class ChowNow_Widget
