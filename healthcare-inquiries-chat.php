<?php
/*
* Plugin Name:       Healthcare Inquiries Chat
* Description:       HealthChat Assistant: Streamline healthcare interactions, collect symptoms, and improve user engagement on your website.
* Version:           0.1.1
* Author:            Huzaifa
* Author URI:        https://github.com/Huzaifa-314
*/


// Shortcode function to display the chatbot
    function display_chatbot() {
        ob_start();
        include(plugin_dir_path(__FILE__) . 'template/chatbot.php');
        return ob_get_clean();
    }

    add_shortcode('display-chatbot', 'display_chatbot');
//


/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
// Register custom fields for the 'hic_questionnaire' post type
    function create_hic_questionnaire_post_type() {
        register_post_type('hic_questionnaire',
            array(
                'labels' => array(
                    'name' => __('Questionnaires'),
                    'singular_name' => __('Questionnaire')
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'hic-questionnaires'),
                'supports' => array(),
            )
        );
    }
    add_action('init', 'create_hic_questionnaire_post_type');

    function register_hic_questionnaire_custom_fields() {
        $hic_custom_fields = array(
            'hic_question_id' => 'Question ID',
            'hic_prev_question_id' => 'Previous ID', // New custom field
            'hic_question' => 'Question',
            'hic_element_type' => 'Element Type',
            'hic_value' => 'Value',
            'hic_next_question_id' => 'Next ID', // New custom field
        );

        foreach ($hic_custom_fields as $hic_field_name => $hic_field_description) {
            register_post_meta('hic_questionnaire', $hic_field_name, array(
                'type' => 'string',
                'description' => $hic_field_description,
                'single' => true,
                'show_in_rest' => true,
            ));
        }
    }

    // Populate the custom fields in the admin column
    function populate_custom_columns_of_hic_questionnaire($column, $post_id) {
        switch ($column) {
            case 'hic_question_id':
            case 'hic_prev_question_id': // New custom field
            case 'hic_question':
            case 'hic_element_type':
            case 'hic_value':
            case 'hic_next_question_id': // New custom field
                $field_value = get_post_meta($post_id, $column, true);
                echo esc_html($field_value);
                break;
        }
    }
    add_action('manage_hic_questionnaire_posts_custom_column', 'populate_custom_columns_of_hic_questionnaire', 10, 2);

    // Add custom columns to the admin column
    function add_custom_columns_to_hic_questionnaire($columns) {
        $hic_custom_columns = array(
            'cb' => $columns['cb'],
            'hic_question_id' => 'Question ID',
            'hic_prev_question_id' => 'Previous ID', // New custom field
            'hic_question' => 'Question',
            'hic_element_type' => 'Element Type',
            'hic_value' => 'Value',
            'hic_next_question_id' => 'Next ID', // New custom field
            'date' => $columns['date'],
        );

        return $hic_custom_columns;
    }
    add_filter('manage_hic_questionnaire_posts_columns', 'add_custom_columns_to_hic_questionnaire');

    // Add custom meta boxes for 'question,' 'element_type,' and 'value' in the post editor
    function add_hic_questionnaire_custom_meta_boxes() {
        add_meta_box(
            'hic_questionnaire_meta',
            'Questionnaire Details',
            'hic_questionnaire_meta_box_callback',
            'hic_questionnaire',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_hic_questionnaire_custom_meta_boxes');

    // Callback function for the custom meta boxes
    function hic_questionnaire_meta_box_callback($post) {
        // Retrieve existing values for 'question,' 'element_type,' and 'value'
        $question_id = get_post_meta($post->ID, 'hic_question_id', true);
        $prev_question_id = get_post_meta($post->ID, 'hic_prev_question_id', true); // New custom field
        $question = get_post_meta($post->ID, 'hic_question', true);
        $element_type = get_post_meta($post->ID, 'hic_element_type', true);
        $value = get_post_meta($post->ID, 'hic_value', true);
        $next_question_id = get_post_meta($post->ID, 'hic_next_question_id', true); // New custom field

        // Output fields for 'question_id,' 'prev_question_id,' 'question,' 'element_type,' 'value,' and 'next_question_id'
        ?>
        <label for="hic_question_id">Question ID:</label>
        <input type="text" id="hic_question_id" name="hic_question_id" value="<?php echo esc_attr($question_id); ?>"><br>

        <label for="hic_prev_question_id">Previous ID:</label>
        <input type="text" id="hic_prev_question_id" name="hic_prev_question_id" value="<?php echo esc_attr($prev_question_id); ?>"><br>

        <label for="hic_question">Question:</label>
        <input type="text" id="hic_question" name="hic_question" value="<?php echo esc_attr($question); ?>"><br>

        <label for="hic_element_type">Element Type:</label>
        <input type="text" id="hic_element_type" name="hic_element_type" value="<?php echo esc_attr($element_type); ?>"><br>

        <label for="hic_value">Value:</label>
        <input type="text" id="hic_value" name="hic_value" value="<?php echo esc_attr($value); ?>"><br>

        <label for="hic_next_question_id">Next ID:</label>
        <input type="text" id="hic_next_question_id" name="hic_next_question_id" value="<?php echo esc_attr($next_question_id); ?>"><br>
        <?php
    }

    // Save custom field values when the post is saved or updated
    function save_hic_questionnaire_custom_meta($post_id) {
        if (isset($_POST['hic_question_id'])) {
            update_post_meta($post_id, 'hic_question_id', sanitize_text_field($_POST['hic_question_id']));
        }
        if (isset($_POST['hic_prev_question_id'])) {
            update_post_meta($post_id, 'hic_prev_question_id', sanitize_text_field($_POST['hic_prev_question_id'])); // New custom field
        }
        if (isset($_POST['hic_question'])) {
            update_post_meta($post_id, 'hic_question', sanitize_text_field($_POST['hic_question']));
        }
        if (isset($_POST['hic_element_type'])) {
            update_post_meta($post_id, 'hic_element_type', sanitize_text_field($_POST['hic_element_type']));
        }
        if (isset($_POST['hic_value'])) {
            update_post_meta($post_id, 'hic_value', sanitize_text_field($_POST['hic_value']));
        }
        if (isset($_POST['hic_next_question_id'])) {
            update_post_meta($post_id, 'hic_next_question_id', sanitize_text_field($_POST['hic_next_question_id'])); // New custom field
        }
    }
    add_action('save_post', 'save_hic_questionnaire_custom_meta');
//


/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
// Register the 'hic_message' custom post type
    function create_hic_message_post_type() { // Modified function name
        register_post_type('hic_message', array( // Modified post type slug
            'labels' => array(
                'name' => __('Messages'),
                'singular_name' => __('Message')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'hic-messages'), // Modified slug
            'supports' => array(),
        ));
    }
    add_action('init', 'create_hic_message_post_type'); // Modified function name

    // Register custom fields for the 'hic_message' post type
    function register_hic_message_custom_fields() { // Modified function name
        $custom_fields = array(
            'hic_sender_id' => 'Sender ID', // Modified custom field name
            'hic_receiver_id' => 'Receiver ID', // Modified custom field name
            'hic_message' => 'Message', // Modified custom field name
        );

        foreach ($custom_fields as $field_name => $field_description) {
            register_post_meta('hic_message', $field_name, array( // Modified post type slug and custom field names
                'type' => 'string',
                'description' => $field_description,
                'single' => true,
                'show_in_rest' => true,
            ));
        }
    }

    // Populate the custom fields in the admin column
    function populate_custom_columns_of_hic_message($column, $post_id) { // Modified function name
        switch ($column) {
            case 'hic_sender_id': // Modified custom field name
            case 'hic_receiver_id': // Modified custom field name
            case 'hic_message': // Modified custom field name
                $field_value = get_post_meta($post_id, $column, true);
                echo esc_html($field_value);
                break;
            // Add more cases if you have additional custom fields to display
        }
    }
    add_action('manage_hic_message_posts_custom_column', 'populate_custom_columns_of_hic_message', 10, 2); // Modified post type slug

    // Add custom columns to the admin column
    function add_custom_columns_to_hic_message($columns) { // Modified function name
        $custom_columns = array(
            'cb' => $columns['cb'],
            'hic_sender_id' => 'Sender ID', // Modified custom field name
            'hic_receiver_id' => 'Receiver ID', // Modified custom field name
            'hic_message' => 'Message', // Modified custom field name
            'date' => $columns['date'],
        );

        return $custom_columns;
    }
    add_filter('manage_hic_message_posts_columns', 'add_custom_columns_to_hic_message'); // Modified post type slug

    // Add custom meta boxes for 'hic_sender_id,' 'hic_receiver_id,' and 'hic_message' in the post editor
    function add_hic_message_custom_meta_boxes() { // Modified function name
        add_meta_box(
            'hic_message_meta',
            'Message Details', // Modified title
            'hic_message_meta_box_callback', // Modified function name
            'hic_message', // Modified post type slug
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_hic_message_custom_meta_boxes'); // Modified function name

    // Callback function for the custom meta boxes
    function hic_message_meta_box_callback($post) { // Modified function name
        // Retrieve existing values for 'hic_sender_id,' 'hic_receiver_id,' and 'hic_message'
        $sender_id = get_post_meta($post->ID, 'hic_sender_id', true); // Modified custom field name
        $receiver_id = get_post_meta($post->ID, 'hic_receiver_id', true); // Modified custom field name
        $message = get_post_meta($post->ID, 'hic_message', true); // Modified custom field name

        // Output fields for 'hic_sender_id,' 'hic_receiver_id,' and 'hic_message'
        ?>
        <label for="hic_sender_id">Sender ID:</label> <!-- Modified label for attribute -->
        <input type="text" id="hic_sender_id" name="hic_sender_id" value="<?php echo esc_attr($sender_id); ?>"><br>
        
        <label for="hic_receiver_id">Receiver ID:</label> <!-- Modified label for attribute -->
        <input type="text" id="hic_receiver_id" name="hic_receiver_id" value="<?php echo esc_attr($receiver_id); ?>"><br>
        
        <label for="hic_message">Message:</label> <!-- Modified label for attribute -->
        <input type="text" id="hic_message" name="hic_message" value="<?php echo esc_attr($message); ?>">
        <?php
    }

    // Save custom field values when the post is saved or updated
    function save_hic_message_custom_meta($post_id) { // Modified function name
        if (isset($_POST['hic_sender_id'])) {
            update_post_meta($post_id, 'hic_sender_id', sanitize_text_field($_POST['hic_sender_id'])); // Modified custom field name
        }
        if (isset($_POST['hic_receiver_id'])) {
            update_post_meta($post_id, 'hic_receiver_id', sanitize_text_field($_POST['hic_receiver_id'])); // Modified custom field name
        }
        if (isset($_POST['hic_message'])) {
            update_post_meta($post_id, 'hic_message', sanitize_text_field($_POST['hic_message'])); // Modified custom field name
        }
    }
    add_action('save_post', 'save_hic_message_custom_meta'); // Modified function name
//




///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//test
    add_action('wp_ajax_fetch_bot_question', 'fetch_bot_question_callback');
    add_action('wp_ajax_nopriv_fetch_bot_question', 'fetch_bot_question_callback');

    function fetch_bot_question_callback() {
        if (isset($_POST['nextQuestionId'])) {
            $next_question_id = sanitize_text_field($_POST['nextQuestionId']);


            $args = array(
                'post_type' => 'hic_questionnaire',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'hic_question_id',
                        'value' => $next_question_id, // This should be a string, not an integer
                        'compare' => '=',
                    ),
                ),
            );
        
            $questions = get_posts($args);
        
            if (!empty($questions)) {
                $question = $questions[0];
                $question_text = get_post_meta($question->ID, 'hic_question', true);
                $question_type = get_post_meta($question->ID, 'hic_element_type', true);
                $value = get_post_meta($question->ID, 'hic_value', true);
                $next_question_id = get_post_meta($question->ID, 'hic_next_question_id', true);
        
                $response = array(
                    'success' => true,
                    'question' => array(
                        'question_text' => $question_text,
                        'type' => $question_type,
                        'value' => $value,
                        'next_question_id' => $next_question_id,
                    ),
                );
        
                echo json_encode($response);
            } else {
                $response = array(
                    'success' => false,
                );
        
                echo json_encode($response);
            }
        
            wp_die();
        }
    }
    

    add_action('wp_ajax_process_user_response', 'process_user_response_callback');
    add_action('wp_ajax_nopriv_process_user_response', 'process_user_response_callback');

    function process_user_response_callback() {
        $user_response = sanitize_text_field($_POST['user_response']);

        $args = array(
            'post_type' => 'hic_questionnaire',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'hic_prev_question_id',
                    'value' => $user_response,
                    'compare' => '=',
                ),
            ),
        );

        $questions = get_posts($args);

        if (!empty($questions)) {
            $question = $questions[0];
            $question_text = get_post_meta($question->ID, 'hic_question', true);
            $question_type = get_post_meta($question->ID, 'hic_element_type', true);
            $options = get_post_meta($question->ID, 'hic_value', true);

            $response = array(
                'success' => true,
                'question' => array(
                    'question_text' => $question_text,
                    'type' => $question_type,
                    'options' => $options,
                ),
            );

            echo json_encode($response);
        } else {
            $response = array(
                'success' => false,
            );

            echo json_encode($response);
        }

        wp_die();
    }

//














//enqueuing scrpts
    //bootstrap
    add_action( 'wp_print_styles', 'add_bootstrap' );
    function add_bootstrap() {
        wp_register_style('prefix_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
        wp_enqueue_style('prefix_bootstrap');
        wp_register_script('prefix_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js');
        wp_enqueue_script('prefix_bootstrap');
    }

    //ajax url
    function hic_ajax_url() {
        echo '<script type="text/javascript">
            var hic_ajax_url = "' . admin_url('admin-ajax.php') . '";
        </script>';
    }
    add_action('wp_head', 'hic_ajax_url');

//