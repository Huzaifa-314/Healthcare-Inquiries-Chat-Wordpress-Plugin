<?php

wp_enqueue_style('chatbot-style', plugins_url('../css/chatbot.css', __FILE__));
wp_enqueue_script('chatbot-script', plugins_url('../js/chatbot.js', __FILE__), array('jquery'), '1.0', true);


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
?>