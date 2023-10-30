<?php
if(current_user_has_enquiry()){
    wp_enqueue_style('chatbox-style4', plugins_url('../css/chatbot.css', __FILE__));
    wp_enqueue_style('queue-chat-style4', plugins_url('../css/queue-chat.css', __FILE__));
    wp_enqueue_script('queue-chat-script4', plugins_url('../js/queue_chat.js', __FILE__), array('jquery'), '1.0', true);
    $target_page_url = get_permalink(get_page_by_path('chatdoc'));
    // print_r($target_page_url);

    ?>
        <div class="chatbox">
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
            <!-- Add more messages as needed -->
            <!-- <div class="message received welcome-message">
                <div class="message-content">Welcome to Prakki Chat! What is the reason for your visit?</div>
            </div> -->





            <div class="d-flex justify-content-center align-items-center w-100 h-100">
            <a href="<?php echo $target_page_url;?>">
                <button class="btn btn-primary">Chat with doctor</button>
            </a>
            </div>
        </div>
    </div>
    <?php

}else{
    wp_enqueue_style('chatbot-style', plugins_url('../css/chatbot.css', __FILE__));
    wp_enqueue_script('chatbot-script', plugins_url('../js/chatbot.js', __FILE__), array('jquery'), '1.0', true);
    
    $chatbot_page_url = get_permalink(get_page_by_path('chatdoc'));
        ?>
        <div class="d-none enquiry_id"></div>
        <a class="d-none chatbot-page-url" href="<?php echo $chatbot_page_url;?>">
        <button class="btn btn-primary">Chat with doctor</button>
        </a>
    <?php
    ?>
    <div class="chatbox">
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
    
    
    
            </div>
            <div class="chatbox-input">
                <div id="user-input">
                    <input class="normal_text_input" type="text" placeholder="Type your response...">
                </div>
    
                <button id="send-button">Send</button>
            </div>
        </div>
    <?php
}


?>