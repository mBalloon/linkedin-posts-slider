<?php

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Elementor Slider Widget.
 *
 * @since 1.0.0
 */
class Elementor_Slider_Widget extends \Elementor\Widget_Base
{

  /**
   * Constructor
   */
  public function __construct($data = [], $args = null)
  {

    // Call parent constructor
    parent::__construct($data, $args);

    // Enqueue styles
    wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
    wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));

    // Enqueue scripts
    wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);
    wp_enqueue_script('swiper-script');

    wp_register_script('slider-script', plugins_url('../public/script.js', __FILE__), ['jquery', 'swiper-script'], false, true);
    wp_localize_script('slider-script', 'ajax_object', [
      'ajax_url' => admin_url('admin-ajax.php')
    ]);

    // Enqueue styles
    wp_enqueue_style('slider-style');

    // Get custom settings
    $settings = array(
      'section-company-color' => get_option('section-company-color'),
      'section-company-font-size' => get_option('section-company-font-size'),
      'section-company-font-family' => get_option('section-company-font-family'),
      'section-company-line-height' => get_option('section-company-line-height'),
      'section-author-date-color' => get_option('section-author-date-color'),
      'section-author-date-font-size' => get_option('section-author-date-font-size'),
      'section-author-date-font-family' => get_option('section-author-date-font-family'),
      'section-author-date-font-weight' => get_option('section-author-date-font-weight'),
      'section-author-date-line-height' => get_option('section-author-date-line-height'),
      'section-body-color' => get_option('section-body-color'),
      'section-body-font-size' => get_option('section-body-font-size'),
      'section-body-font-family' => get_option('section-body-font-family'),
      'section-body-webkit-line-clamp' => get_option('section-body-webkit-line-clamp'),
      'section-interactions-color' => get_option('section-interactions-color'),
      'section-interactions-font-size' => get_option('section-interactions-font-size'),
      'section-interactions-font-family' => get_option('section-interactions-font-family'),
      'section-interactions-font-weight' => get_option('section-interactions-font-weight'),
      'section-interactions-line-height' => get_option('section-interactions-line-height'),
    );

    // Add custom CSS 
    $custom_css = "
          // Company Name
          .section-company {
            color: {$settings['section-company-color']};
            font-size: {$settings['section-company-font-size']}px; 
            font-family: {$settings['section-company-font-family']};
            line-height: {$settings['section-company-line-height']}px;
          }
          
          // Author and Date 
          .section-author-date {
            color: {$settings['section-author-date-color']};
            font-size: {$settings['section-author-date-font-size']}px;
            font-family: {$settings['section-author-date-font-family']};
            font-weight: {$settings['section-author-date-font-weight']};
            line-height: {$settings['section-author-date-line-height']}px;
          }
    
          // Post Text
          .section-body {
            color: {$settings['section-body-color']};
            font-size: {$settings['section-body-font-size']}px;
            font-family: {$settings['section-body-font-family']};
            -webkit-line-clamp: {$settings['section-body-webkit-line-clamp']};
          }
    
          // Interactions
          .section-interactions {
            color: {$settings['section-interactions-color']};
            font-size: {$settings['section-interactions-font-size']}px;
            font-family: {$settings['section-interactions-font-family']}; 
            font-weight: {$settings['section-interactions-font-weight']};
            line-height: {$settings['section-interactions-line-height']}px;
          }
        ";

    wp_add_inline_style('slider-style', $custom_css);
  }

  /**
   * Get widget name
   */
  public function get_name()
  {
    return 'Linkedin Slider';
  }

  /**
   * Get widget title
   */
  public function get_title()
  {
    return __('Linkedin Slider', 'linkedin-slider');
  }

  /**
   * Get widget icon
   */
  public function get_icon()
  {
    return 'eicon-slider-album';
  }

  /**
   * Get widget categories
   */
  public function get_categories()
  {
    return ['general'];
  }

  /**
   * Register widget controls
   */
  protected function register_controls()
  {

    // Custom CSS
    $this->start_controls_section(
      'section_custom_css',
      [
        'label' => __('Custom CSS', 'linkedin-slider'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'custom_css',
      [
        'label' => __('Custom CSS', 'linkedin-slider'),
        'type' => \Elementor\Controls_Manager::CODE,
        'language' => 'css',
        'rows' => 10,
        'default' => '',
        'description' => __('Add custom CSS here', 'linkedin-slider'),
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Render widget output
   */
  protected function render()
  {

    // Enqueue assets
    wp_enqueue_style('slider-style');
    wp_enqueue_script('slider-script');

?>

    <div class="swiper">
      <div class="swiper-wrapper">
        <!-- Slides added dynamically via JS -->
      </div>

      <!-- Arrows -->
      <div class="next-right-arrow">
        <button type="button" class="swiper-button-next">
          <svg>...</svg>
        </button>
      </div>

      <div class="pre-left-arrow">
        <button type="button" class="swiper-button-prev">
          <svg>...</svg>
        </button>
      </div>

    </div>

<?php

  }
}
