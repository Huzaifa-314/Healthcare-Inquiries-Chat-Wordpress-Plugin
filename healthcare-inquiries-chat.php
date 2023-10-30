<?php
/*
* Plugin Name:       Healthcare Inquiries Chat
* Description:       HealthChat Assistant: Streamline healthcare interactions, collect symptoms, and improve user engagement on your website.
* Version:           0.1.1
* Author:            Huzaifa
* Author URI:        https://github.com/Huzaifa-314
*/


//custom user role
// Add the custom 'doctor' user role
    function add_custom_doctor_role() {
        add_role(
            'doctor',
            'Doctor',
            array(
                'read' => true,  // Doctors can read content
                'edit_posts' => false,  // Doctors cannot edit posts
                'delete_posts' => false,  // Doctors cannot delete posts
                'edit_pages' => true,  // Doctors can edit pages
                'delete_pages' => false,  // Doctors cannot delete pages
                'upload_files' => true,  // Doctors can upload files
                // Add or modify capabilities as needed
            )
        );
    }

    add_action('init', 'add_custom_doctor_role');
//



// Shortcode function to display the chatbot
    function display_chatbot() {
        ob_start();
        include(plugin_dir_path(__FILE__) . 'template/chatbot.php');
        return ob_get_clean();
    }

    add_shortcode('display-chatbot', 'display_chatbot');
//

// Shortcode function to display the chatdoc
    function display_chatdoc() {
        ob_start();
        include(plugin_dir_path(__FILE__) . 'template/chatdoc.php');
        return ob_get_clean();
    }

    add_shortcode('display-chatdoc', 'display_chatdoc');
//


// Shortcode function to display the chatdoc
function display_doctor_panel() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'template/doctor_panel.php');
    return ob_get_clean();
}

add_shortcode('display-doctor-panel', 'display_doctor_panel');
//





$chatbot_page_id = -1;
$chatdoc_page_id = -1;


/////////////////////////////////////
//create doctor panel page
    add_action('init', 'create_chatbot_page');

    // Function to create a page and add a shortcode
    function create_chatbot_page() {
        $post_id = -1; // Initialize with -1 to check if the page exists

        // Check if the "queue-chat" page already exists
        $page = get_page_by_path('chatbot');

        if ($page) {
            // The page already exists, so update its content
            $post_id = $page->ID;
        } else {
            // Create a new page
            $post = array(
                'post_title'   => 'Chatbot',
                'post_name'    => 'chatbot',
                'post_content' => '[display-chatbot]', // Shortcode and content
                'post_status'  => 'publish',
                'post_type'    => 'page',
            );
            $post_id = wp_insert_post($post);
        }
        $chatbot_page_id = $post_id;
        return $post_id;
    }
//


/////////////////////////////////////
//create doctor_panel page
add_action('init', 'create_doctor_panel_page');

// Function to create a page and add a shortcode
function create_doctor_panel_page() {
    $post_id = -1; // Initialize with -1 to check if the page exists

    // Check if the "queue-chat" page already exists
    $page = get_page_by_path('doctor_panel');

    if ($page) {
        // The page already exists, so update its content
        $post_id = $page->ID;
    } else {
        // Create a new page
        $post = array(
            'post_title'   => 'doctor_panel',
            'post_name'    => 'doctor_panel',
            'post_content' => '[display-doctor-panel]', // Shortcode and content
            'post_status'  => 'publish',
            'post_type'    => 'page',
        );
        $post_id = wp_insert_post($post);
    }
    $doctor_panel_page_id = $post_id;
    return $post_id;
}
//


//////////////////////////////////////////////
//create chatdoc page
    add_action('init', 'create_chatdoc_page');

    // Function to create a page and add a shortcode
    function create_chatdoc_page() {
        $post_id = -1; // Initialize with -1 to check if the page exists

        // Check if the "queue-chat" page already exists
        $page = get_page_by_path('chatdoc');

        if ($page) {
            // The page already exists, so update its content
            $post_id = $page->ID;
        } else {
            // Create a new page
            $post = array(
                'post_title'   => 'Chatdoc',
                'post_name'    => 'chatdoc',
                'post_content' => '[display-chatdoc]', // Shortcode and content
                'post_status'  => 'publish',
                'post_type'    => 'page',
            );
            $post_id = wp_insert_post($post);
        }
        $chatdoc_page_id = $post_id;
        return $post_id;
    }
//


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
            'hic_question_slug' => 'Question Slug', // New custom field
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
            case 'hic_question_slug': // New custom field
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
            'hic_question_slug' => 'Question Slug', // New custom field
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
        $prev_question_id = get_post_meta($post->ID, 'hic_question_slug', true); // New custom field
        $question = get_post_meta($post->ID, 'hic_question', true);
        $element_type = get_post_meta($post->ID, 'hic_element_type', true);
        $value = get_post_meta($post->ID, 'hic_value', true);
        $next_question_id = get_post_meta($post->ID, 'hic_next_question_id', true); // New custom field

        // Output fields for 'question_id,' 'prev_question_id,' 'question,' 'element_type,' 'value,' and 'next_question_id'
        ?>
        <label for="hic_question_id">Question ID:</label>
        <input type="text" id="hic_question_id" name="hic_question_id" value="<?php echo esc_attr($question_id); ?>"><br>

        <label for="hic_question_slug">Question Slug:</label>
        <input type="text" id="hic_question_slug" name="hic_question_slug" value="<?php echo esc_attr($prev_question_id); ?>"><br>

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
        if (isset($_POST['hic_question_slug'])) {
            update_post_meta($post_id, 'hic_question_slug', sanitize_text_field($_POST['hic_question_slug'])); // New custom field
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
            'hic_enquiry_id' => 'Enquiry ID', // Modified custom field name
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
            case 'hic_enquiry_id': // Modified custom field name
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
            'hic_enquiry_id' => 'Enquiry ID', // Modified custom field name
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
        $enquiry_id = get_post_meta($post->ID, 'hic_enquiry_id', true); // Modified custom field name
        $sender_id = get_post_meta($post->ID, 'hic_sender_id', true); // Modified custom field name
        $receiver_id = get_post_meta($post->ID, 'hic_receiver_id', true); // Modified custom field name
        $message = get_post_meta($post->ID, 'hic_message', true); // Modified custom field name

        // Output fields for 'hic_sender_id,' 'hic_receiver_id,' and 'hic_message'
        ?>
        <label for="hic_enquiry_id">Enquiry ID:</label> <!-- Modified label for attribute -->
        <input type="text" id="hic_enquiry_id" name="hic_enquiry_id" value="<?php echo esc_attr($enquiry_id); ?>"><br>

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
        if (isset($_POST['hic_enquiry_id'])) {
            update_post_meta($post_id, 'hic_enquiry_id', sanitize_text_field($_POST['hic_enquiry_id'])); // Modified custom field name
        }
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





//////////////////////////////////////////////
//////////////////////////////////////////////
// Register the 'Enquiry' custom post type
    function create_enquiry_post_type() {
        register_post_type('enquiry', array(
            'labels' => array(
                'name' => __('Enquiries'),
                'singular_name' => __('Enquiry'),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'enquiries'),
            'supports' => array(),
        ));
    }
    add_action('init', 'create_enquiry_post_type');

    // Register custom fields for the 'enquiry' post type
    function register_enquiry_custom_fields() {
        $custom_fields = array(
            'hic_user_id' => 'User ID',
            'hic_user_name' => 'Name',
            'hic_has_answered' => 'Has Answered',
        );

        foreach ($custom_fields as $field_name => $field_description) {
            register_post_meta('enquiry', $field_name, array(
                'type' => 'string',
                'description' => $field_description,
                'single' => true,
                'show_in_rest' => true,
            ));
        }
    }

    // Populate the custom columns in the admin column
    function populate_custom_columns_of_enquiry($column, $post_id) {
        switch ($column) {
            case 'hic_user_id':
            case 'hic_user_name':
            case 'hic_has_answered':
                $field_value = get_post_meta($post_id, $column, true);
                echo esc_html($field_value);
                break;
            default:
                // Handle custom fields that start with 'hic_rec_'
                $custom_fields_data = get_post_custom($post_id);
                foreach ($custom_fields_data as $custom_field_name => $custom_field_value) {
                    if (strpos($custom_field_name, 'hic_rec_') === 0) {
                        switch($column){
                            case $custom_field_name:
                                echo esc_html($custom_field_value[0]);
                                break;
                        }
                    }
                }
        }
    }
    add_action('manage_enquiry_posts_custom_column', 'populate_custom_columns_of_enquiry', 10, 2);


    add_action('manage_posts_custom_column', 'custom_column_content', 10, 2);

    function custom_column_content($column_name, $post_id) {
        if ($column_name == 'post_id') {
            echo $post_id;
        }
    }

    // Add custom columns to the admin column
    function add_custom_columns_to_enquiry($columns) {
        $custom_columns = array(
            'cb' => $columns['cb'],
            'post_id' => 'Post ID',
            'date' => $columns['date'],
            'hic_user_id' => 'User ID',
            'hic_user_name' => 'Name',
            'hic_has_answered' => 'Has Answered',
        );

        // Filter custom fields and add those that start with 'hic_rec_'
        $post_ids = get_posts(array('post_type' => 'enquiry', 'numberposts' => -1));
        foreach ($post_ids as $post_id) {
            $custom_fields = get_post_custom_keys($post_id->ID);
            foreach ($custom_fields as $field) {
                if (strpos($field, 'hic_rec_') === 0) {
                    $custom_columns[$field] = $field;
                }
            }
        }

        return $custom_columns;
    }
    add_filter('manage_enquiry_posts_columns', 'add_custom_columns_to_enquiry');

    // Add custom meta boxes for custom fields in the post editor
    function add_enquiry_custom_meta_boxes() {
        add_meta_box(
            'enquiry_meta',
            'Enquiry Details',
            'enquiry_meta_box_callback',
            'enquiry',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_enquiry_custom_meta_boxes');

    // Callback function for the custom meta boxes
    function enquiry_meta_box_callback($post) {
        $custom_fields = array(
            'hic_user_id' => 'User ID',
            'hic_user_name' => 'Name',
            'hic_has_answered' => 'Has Answered',
        );

        foreach ($custom_fields as $field_name => $field_description) {
            $field_value = get_post_meta($post->ID, $field_name, true);
            ?>
            <label for="<?php echo $field_name; ?>"><?php echo $field_description; ?>:</label>
            <input type="text" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php echo esc_attr($field_value); ?>"><br>
            <?php
        }
        
        // Display custom fields that start with 'hic_rec_'
        $custom_fields = get_post_custom($post->ID);
        foreach ($custom_fields as $field_name => $field_value) {
            if (strpos($field_name, 'hic_rec_') === 0) {
                ?>
                <label for="<?php echo $field_name; ?>"><?php echo $field_name; ?>:</label>
                <input type="text" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php echo esc_attr($field_value[0]); ?>"><br>
                <?php
            }
        }
    }

    // Save custom field values when the post is saved or updated
    function save_enquiry_custom_meta($post_id) {
        $custom_fields = array(
            'hic_date',
            'hic_user_id',
            'hic_user_name',
            'hic_has_answered',
        );

        foreach ($custom_fields as $field_name) {
            if (isset($_POST[$field_name])) {
                update_post_meta($post_id, $field_name, sanitize_text_field($_POST[$field_name]));
            }
        }
        
        // Save custom fields that start with 'hic_rec_'
        foreach ($_POST as $field_name => $field_value) {
            if (strpos($field_name, 'hic_rec_') === 0) {
                update_post_meta($post_id, $field_name, sanitize_text_field($field_value));
            }
        }
    }
    add_action('save_post', 'save_enquiry_custom_meta');

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
                $question_slug = get_post_meta($question->ID, 'hic_question_slug', true);
                $question_type = get_post_meta($question->ID, 'hic_element_type', true);
                $value = get_post_meta($question->ID, 'hic_value', true);
                $next_question_id = get_post_meta($question->ID, 'hic_next_question_id', true);
        
                $response = array(
                    'success' => true,
                    'question' => array(
                        'text' => $question_text,
                        'slug' => $question_slug,
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




    add_action('wp_ajax_save_answers', 'save_answers_callback');
    add_action('wp_ajax_nopriv_save_answers', 'save_answers_callback');

    function save_answers_callback() {
        if (isset($_POST['answers'])) {
            $answers = $_POST['answers'];

            // Check if the user is logged in
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                $hic_user_id = $user->ID;
                $hic_user_name = $user->display_name;
            } else {
                // Generate a random user name (you can replace this logic as needed)
                $hic_user_id = session_id(); // Set to a default value or generate a unique ID
                $hic_user_name = 'Guest'.$hic_user_id; // Generate a random name
            }

            // Store answers in custom fields with keys starting with 'hic_rec'
            $post_id = wp_insert_post(array(
                'post_type' => 'enquiry',
                'post_status' => 'publish',
            ));

            foreach ($answers as $key => $answer) {
                // Prepend 'hic_rec' to the answer's key
                $custom_field_key = 'hic_rec_' . $key;

                // Check if the answer is an array
                if (is_array($answer)) {
                    // Join array values with the "|" separator and store as a string
                    $concatenated_answer = implode('|', $answer);
                    update_post_meta($post_id, $custom_field_key, sanitize_text_field($concatenated_answer));
                } else {
                    update_post_meta($post_id, $custom_field_key, sanitize_text_field($answer));
                }
            }

            // Set 'hic_user_id', 'hic_user_name', and 'hic_has_answered' custom fields
            update_post_meta($post_id, 'hic_user_id', $hic_user_id);
            update_post_meta($post_id, 'hic_user_name', $hic_user_name);
            update_post_meta($post_id, 'hic_has_answered', 0);

            wp_send_json_success(array('message' => 'Answers saved successfully'));
        } else {
            wp_send_json_error(array('message' => 'Answers not provided'));
        }
    }

    add_action('init', 'myStartSession', 1);
    add_action('wp_logout', 'myEndSession');
    add_action('wp_login', 'myEndSession');

    function myStartSession() {
        if(!session_id()) {
            session_start();
        }
    }

    function myEndSession() {
        session_destroy ();
    }


    function current_user_has_enquiry() {
        // Get the current user's ID or session ID for anonymous users
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
        } else {
            // Generate a random user name (you can replace this logic as needed)
            $current_user_id = session_id(); // Set to a default value or generate a unique ID
        }
    
        // Define the custom post type (change 'enquiry' to your custom post type slug)
        $post_type = 'enquiry';
    
        // Prepare the query to check if the user has a post in the custom post type
        $query = new WP_Query(array(
            'post_type' => $post_type,
            'meta_query' => array(
                array(
                    'key' => 'hic_user_id',
                    'value' => $current_user_id,
                    'compare' => '=',
                ),
            ),
            'posts_per_page' => 1, // You only need to check if they have at least one post
        ));
    
        // Check if there are posts for the current user
        if ($query->have_posts()) {
            return true; // The user has a post in the custom post type
        } else {
            return false; // The user doesn't have a post in the custom post type
        }
    }
    

    

//









/////////////////////////////////////////////
////////////////////////////////////////////
//chatbox functionality
    add_action('wp_ajax_fetch_conversations', 'fetch_conversations_callback');
    add_action('wp_ajax_nopriv_fetch_conversations', 'fetch_conversations_callback');

    function fetch_conversations_callback() {
        // Check if the enquiry ID is provided via the AJAX request
        if (isset($_POST['enquiry_id'])) {
            $enquiry_id = intval($_POST['enquiry_id']); // Sanitize and get the enquiry ID

            // Query the message posts related to the given enquiry ID
            $conversations = get_posts(array(
                'post_type' => 'hic_message',
                'meta_key' => 'hic_enquiry_id',
                'meta_value' => $enquiry_id,
                'posts_per_page' => -1, // Retrieve all posts
                'order' => 'ASC', // Sort posts in descending order by date
            ));

            $messages = array();

            foreach ($conversations as $conversation) {
                // Retrieve post meta values for each conversation
                $sender_id = get_post_meta($conversation->ID, 'hic_sender_id', true);
                $receiver_id = get_post_meta($conversation->ID, 'hic_receiver_id', true);
                $message_content = get_post_meta($conversation->ID, 'hic_message', true);

                // Create an array for each message
                $message = array(
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'message' => $message_content,
                );

                $messages[] = $message;
            }

            // Return the messages in JSON format
            wp_send_json($messages);
        } else {
            // Return an error response if no enquiry ID is provided
            wp_send_json_error('Enquiry ID not provided');
        }
    }



    // Create a function to send a message
    function send_message() {
        // Check if the request is coming from a valid source
        // check_ajax_referer('my_nonce', 'security');
    
        // Get the enquiry ID and message from the AJAX request
        $enquiry_id = isset($_POST['enquiry_id']) ? intval($_POST['enquiry_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
    
        if (empty($message) || $enquiry_id === 0) {
            wp_send_json_error('Invalid data received.');
        }
    
        // You can add additional checks here, e.g., to verify the user's permissions.
    
        // Determine the sender and receiver IDs based on user roles, etc.
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
        } else {
            // Generate a random user name (you can replace this logic as needed)
            $current_user_id = session_id(); // Set to a default value or generate a unique ID
        }
        $receiver_id = '10'; // Replace with the appropriate receiver's user ID
    
        // Create a new message post
        $message_post_id = wp_insert_post(array(
            'post_type' => 'hic_message',
            'post_title' => '',
            'post_content' => $message,
            'post_status' => 'publish',
            'post_author' => $current_user_id,
            'meta_input' => array(
            'hic_enquiry_id' => $enquiry_id,
                // 'senders_id' => $current_user_id,
                // 'receiver_id' => $receiver_id,
            ),
        ));

            // Update the sender's and receiver's IDs for the message
            update_post_meta($message_post_id, 'hic_sender_id', $current_user_id);
            update_post_meta($message_post_id, 'hic_receiver_id', $receiver_id);
            update_post_meta($message_post_id, 'hic_message', $message);
    }
    

    // Hook the send_message function to the WordPress AJAX action
    add_action('wp_ajax_send_message', 'send_message');
    add_action('wp_ajax_nopriv_send_message', 'send_message');





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