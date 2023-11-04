<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// This function fetches the custom style setting from the database or returns the default value
function get_custom_setting($setting_name, $default_value)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'linkedin_slider_settings';
  $value = $wpdb->get_var($wpdb->prepare(
    "SELECT value FROM $table_name WHERE name = %s",
    $setting_name
  ));
  return ($value !== null) ? $value : $default_value;
}

// This class defines the Elementor Slider Widget
class Elementor_Slider_Widget extends \Elementor\Widget_Base
{

  // Register widget styles and scripts
  public function get_style_depends()
  {
    return ['slider-style', 'swiper-style'];
  }

  public function get_script_depends()
  {
    return ['swiper-script', 'slider-script'];
  }

  // Constructor for the widget
  public function __construct($data = [], $args = null)
  {
    parent::__construct($data, $args);

    // Register styles
    wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
    wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));

    // Register scripts
    wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);
    wp_register_script('slider-script', plugins_url('../public/script.js', __FILE__), ['jquery', 'swiper-script'], false, true);
  }
  private function generate_custom_css()
  {
    $custom_css = "
      .section-company {
        color: " . get_custom_setting('section-company-color', '#454545') . ";
        font-size: " . get_custom_setting('section-company-font-size', '16px') . ";
        font-family: " . get_custom_setting('section-company-font-family', '\"Titillium Web\"') . ";
        line-height: " . get_custom_setting('section-company-line-height', '21px') . ";
      }
      .section-author-date {
        color: " . get_custom_setting('section-author-date-color', '#454545') . ";
        font-size: " . get_custom_setting('section-author-date-font-size', '14px') . ";
        font-family: " . get_custom_setting('section-author-date-font-family', '\"Titillium Web\"') . ";
        font-weight: " . get_custom_setting('section-author-date-font-weight', '300') . ";
        line-height: " . get_custom_setting('section-author-date-line-height', '18px') . ";
      }
      .section-body {
        color: " . get_custom_setting('section-body-color', '#adb5bd') . ";
        font-size: " . get_custom_setting('section-body-font-size', '16px') . ";
        font-family: " . get_custom_setting('section-body-font-family', '\"Titillium Web\"') . ";
        -webkit-line-clamp: " . get_custom_setting('section-body-webkit-line-clamp', '5') . ";
      }
      .section-interactions {
        color: " . get_custom_setting('section-interactions-color', '#454545') . ";
        font-size: " . get_custom_setting('section-interactions-font-size', '14px') . ";
        font-family: " . get_custom_setting('section-interactions-font-family', '\"Titillium Web\"') . ";
        font-weight: " . get_custom_setting('section-interactions-font-weight', '300') . ";
        line-height: " . get_custom_setting('section-interactions-line-height', '18px') . ";
      }
    ";
    return $custom_css;
  }
  // Widget name
  public function get_name()
  {
    return 'linkedin-slider';
  }

  // Widget title
  public function get_title()
  {
    return esc_html__('LinkedIn Posts Slider', 'elementor-slider-widget');
  }

  // Widget icon
  public function get_icon()
  {
    return 'eicon-slider-album';
  }

  // Widget categories
  public function get_categories()
  {
    return ['general'];
  }

  // Register controls
  protected function register_controls()
  {

    // 1h. Custom CSS box control
    $this->start_controls_section(
      'custom_css_section',
      [
        'label' => __('Custom CSS', 'elementor-slider-widget'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'custom_css',
      [
        'label' => __('Custom CSS', 'elementor-slider-widget'),
        'type' => \Elementor\Controls_Manager::CODE,
        'language' => 'css',
        'rows' => 10,
        'default' => '',
        'description' => __('Add your custom CSS code here. It will be applied to the frontend.', 'elementor-slider-widget'),
      ]
    );

    $this->end_controls_section();
  }

  // Render the widget
  protected function render()
  {
    $settings = $this->get_settings_for_display();
    // Enqueue the styles and scripts
    wp_enqueue_style('swiper-style');
    wp_enqueue_style('slider-style');
    wp_enqueue_script('swiper-script');
    wp_enqueue_script('slider-script');

    // Localize script for AJAX URL
    wp_localize_script('slider-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    // Print the custom CSS
    $custom_css = $this->generate_custom_css();
    if (!empty($custom_css)) {
      echo '<style>' . $custom_css . '</style>';
    }
?>
    <div class="swiper">
      <div class="swiper-wrapper">
        <!-- Slides will be added dynamically by JS -->
      </div>
      <div class="next-right-arrow"><button type="button" class="swiper-button-next"></button></div>
      <div class="pre-left-arrow"><button type="button" class="swiper-button-prev"></button></div>
    </div>
<?php
  }
}

// Register the widget
function register_slider_widget($widgets_manager)
{
  require_once plugin_dir_path(__FILE__) . 'slider-widget.php';
  $widgets_manager->register(new \Elementor_Slider_Widget());
}

add_action('elementor/widgets/register', 'register_slider_widget');
