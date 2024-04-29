<?php

namespace IncsubTest;

/**
 * Main class for the plugin
 * Handles the shortcode, data saving via REST API, get data via REST API
 * @since   1.0.0
 * @package IncsubTest
 */
class Main
{

  protected $table;

  /**
   * Consutructor
   * @author  Ridwan Arifandi
   * @since   1.0.0
   * @return  void
   */
  public function __construct()
  {
    global $wpdb;

    $this->table = $wpdb->prefix . INCSUB_TABLE;

    add_action('init',                    [$this, 'register_shortcodes']);
    add_action('rest_api_init',           [$this, 'register_rest_api']);
    add_action('wp_enqueue_scripts',      [$this, 'enqueue_scripts']);
  }

  /**
   * Register the shortcode
   * @since   1.0.0
   * @return  void
   */
  public function register_shortcodes()
  {
    add_shortcode('incsub_display_data',  [$this, 'view_data']);
    add_shortcode('incsub_form',          [$this, 'view_form']);
  }

  /**
   * Display the table via shortcode
   * @since   1.0.0
   * @return  string
   */
  public function view_data()
  {
    ob_start();
    require_once INCSUB_TEST_PATH . 'views/view-data.php';
    return ob_get_clean();
  }

  /**
   * Display the form via shortcode
   * @since   1.0.0
   * @return  string
   */
  public function view_form()
  {
    ob_start();
    require_once INCSUB_TEST_PATH . 'views/view-form.php';
    return ob_get_clean();
  }

  public function enqueue_scripts()
  {
    global $post;

    if (
      empty($post) ||
      !has_shortcode($post->post_content, 'incsub_form') ||
      !has_shortcode($post->post_content, 'incsub_display_data')
    )
      return;

    wp_register_style('datatable', 'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css', [], '1.10.21');
    wp_enqueue_style('incsub-test', INCSUB_TEST_URL . 'assets/css/style.css', ['datatable'], '1.0.0');

    wp_register_script('datatable', 'https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js', ['jquery'], '1.10.21', true);

    wp_enqueue_script('incsub-test', INCSUB_TEST_URL . 'assets/js/main.js', ['jquery', 'datatable'], '1.0.0', true);
    wp_localize_script('incsub-test', 'incsub_test', [
      'rest_url' => rest_url('incsub-test/v1/data'),
      'nonce' => wp_create_nonce('wp_rest')
    ]);
  }

  /**
   * Register the REST API
   * @since   1.0.0
   * @return  void
   */
  public function register_rest_api()
  {
    register_rest_route('incsub-test/v1', '/data', array(
      'methods' => 'GET',
      'callback' => array($this, 'get_data'),
      'permission_callback' => function () {
        // only accessible for logged in users
        return current_user_can('read');
      }
    ));

    register_rest_route('incsub-test/v1', '/data', array(
      'methods' => 'POST',
      'callback' => array($this, 'save_data'),
      'permission_callback' => function () {
        // only accessible for logged in users
        return current_user_can('read');
      }
    ));
  }

  /**
   * Get data from the database via REST API
   * @since   1.0.0
   * @return  array
   */
  public function get_data()
  {
    try {

      global $wpdb;
      $results = $wpdb->get_results("SELECT * FROM " . $this->table);
      return ['data' => $results];
    } catch (\Exception $e) {
      return ['error' => $e->getMessage()];
    }
  }

  /**
   * Validate the data before saving it to the database
   * @since   1.0.0
   * @param   array $data
   * @return  array
   */
  protected function validate_data(array $data): array
  {
    $data = wp_parse_args($data, [
      'name' => '',
      'email' => '',
      'phone' => '',
      'address' => ''
    ]);

    $error = false;
    $messages = [];

    // sanitize data

    $data = array_map('sanitize_text_field', $data);

    // validate data
    if (empty($data['name'])) :
      $error = true;
      $messages[] = 'Name is required';
    endif;

    if (empty($data['email'])) :
      $error = true;
      $messages[] = 'Email is required';
    endif;

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) :
      $error = true;
      $messages[] = 'Email is not valid';
    endif;

    if (empty($data['phone'])) :
      $error = true;
      $messages[] = 'Phone is required';
    endif;

    if (empty($data['address'])) :
      $error = true;
      $messages[] = 'Address is required';
    endif;

    if ($error)
      throw new \Exception(implode('|', $messages));

    return (array) $data;
  }

  /**
   * Save the data to the database via REST API
   * @since   1.0.0
   * @param   WP_REST_Request $request
   * @return  array
   */
  public function save_data($request)
  {
    try {
      global $wpdb;

      $data = $this->validate_data(
        $request->get_params()
      );

      $data['created_at'] = date('Y-m-d H:i:s');

      $wpdb->insert($wpdb->prefix . INCSUB_TABLE, $data, [
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
      ]);

      $id = $wpdb->insert_id;

      return [
        'valid' => true,
        'messages' => ['Data saved'],
        'data' => [
          'id' => $id
        ]
      ];
    } catch (\Exception $e) {
      return [
        'valid' => false,
        'messages' => explode('|', $e->getMessage())
      ];
    }
  }
}
