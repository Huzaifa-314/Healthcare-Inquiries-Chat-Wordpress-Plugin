<?php

$current_user = wp_get_current_user();
if (in_array('doctor', $current_user->roles)) {
    wp_enqueue_style('chatbot-style', plugins_url('../css/chatbot.css', __FILE__));
    wp_enqueue_style('queue-chat-style', plugins_url('../css/queue-chat.css', __FILE__));
    wp_enqueue_script('queue-chat-script', plugins_url('../js/queue_chat.js', __FILE__), array('jquery'), '1.0', true);



    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
    
        // Create a custom query to retrieve the user's enquiry based on their ID
        $enquiry_query = new WP_Query(array(
            'post_type' => 'enquiry',
            // 'author' => $current_user->ID,
        ));
    
        if ($enquiry_query->have_posts()) {
            // Display the user's enquiry in a beautiful format
            while ($enquiry_query->have_posts()) {
                $enquiry_query->the_post();
    
                $user_id = get_post_meta(get_the_ID(), 'user_id', true);
                $reason = get_post_meta(get_the_ID(), 'reason', true);
                $symptom_starting_date = get_post_meta(get_the_ID(), 'symptom_starting_date', true);
                $symptoms = get_post_meta(get_the_ID(), 'symptoms', true);
                $additional_symptoms = get_post_meta(get_the_ID(), 'additional_symptoms', true);
    
                // Customize the HTML structure for displaying the enquiry
                ?>
                <!-- <div class="enquiry-summary">
                    <h3>Thank you. You have answered the questionnaire. Please wait. Average wait time is 1h 1min 1sec. The doctor will be with you shortly</h3>
                    <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
                    <p><strong>Reason:</strong> <?php echo $reason; ?></p>
                    <p><strong>Symptom Start Date:</strong> <?php echo $symptom_starting_date; ?></p>
                    <p><strong>Symptoms:</strong> <?php echo $symptoms; ?></p>
                    <p><strong>Additional Symptoms:</strong> <?php echo $additional_symptoms; ?></p>
                </div> -->
                <?php
            }
        }else{
            echo 'No enquiry found for the current user.';
        }
    
        wp_reset_postdata();
    }
    ?>
        <div class="chatbox">
            <div class="chatbox-area">
            <div class="chatbox-sidebar">
                    <h4 class="sidebar-header">Enquiries</h4>
                    <div class="sidebar-content">
                        <?php
                        if (is_user_logged_in()) {
                            $current_user_id = get_current_user_id();
                        } else {
                            $current_user_id = session_id();
                        }

                        // Create a custom query to retrieve the user's enquiry based on their ID
                        $enquiry_query = new WP_Query(array(
                            'post_type' => 'enquiry',
                            'posts_per_page' => -1,
                            'meta_key' => 'hic_user_id',  // Replace with your custom field key
                            // 'meta_value' => $current_user_id,
                        ));

                        if ($enquiry_query->have_posts()) {
                            // Display the user's enquiries in a beautiful format
                            $serial = 0;
                            while ($enquiry_query->have_posts()) {
                                $enquiry_query->the_post();
                                $serial++;
                                $enquiry_id = get_the_ID();

                                // Retrieve custom fields
                                $user_id = get_post_meta($enquiry_id, 'hic_user_id', true);  // Replace with your custom field key
                                $user_name = get_post_meta($enquiry_id, 'hic_user_name', true);  // Replace with your custom field key
                                $has_answered = get_post_meta($enquiry_id, 'hic_has_answered', true);  // Replace with your custom field key

                                // Customize the HTML structure for displaying the enquiry
                                ?>
                                <div class="enquiry-summary" data-enquiry-id="<?php echo $enquiry_id; ?>">
                                    <div class="d-none current_user_id"><?php
                                        if (is_user_logged_in()) {
                                            echo get_current_user_id();
                                        } else {
                                            // Generate a random user name (you can replace this logic as needed)
                                            echo session_id(); // Set to a default value or generate a unique ID
                                        }
                                    ?></div>
                                    <div class="serial"><h3 class="m-0"><?php echo $serial; ?></h3></div>
                                    <div class="main-enquiry-part">
                                        <div class="name"><?php echo $user_name; ?></div>
                                    </div>
                                    <div class="has-answered">
                                        <div class="has-answered-indicator <?php
                                            if ($has_answered == "0") echo 'not-answered';
                                        ?>"></div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo 'No enquiries found for the current user.';
                        }

                        wp_reset_postdata();
                        ?>
                    </div>
                </div>


                <div class="chatbox-right">
                    <div class="chatbox-header">
                    <div class="user-avatar">
                        <img src="<?php echo plugin_dir_url(__FILE__) . 'chatbot.png'; ?>" alt="User Avatar">
                    </div>
                    <div class="user-info">
                        <div class="user-name">Prakki Bot</div>
                        <!-- <div class="user-status">Online</div> -->
                    </div>
                </div>
                <div class="chatbox-messages">
                    <!-- <div class="message received">
                        <div class="message-content">Hello! How can I help you?</div>
                    </div>
                    <div class="message sent">
                        <div class="message-content">I have a question about your services.</div>
                    </div> -->
                    <div class="d-flex justify-content-center align-items-center w-100 h-100">
                        <h3>Select a chat</h3>
                    </div>


                </div>
                <div class="chatbox-input">
                    <div id="user-input">
                        <input class="normal_text_input" type="text" id="message-input" placeholder="Type your response...">
                    </div>
                    <button id="send-button">Send</button>
                </div>

                </div>
            </div>
        </div>

    <?php
} else {
    echo 'you dont have access to the page';
}

    


?>