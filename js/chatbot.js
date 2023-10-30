jQuery(document).ready(function ($) {
    var currentQuestion = null; // Store the current question
    var selectOptions = null; // Store the options for select type questions
    var nextQuestionId = 'first_question';

    // Add a message to the chatbox
    function addMessage(type, content) {
        var messageClass = type === 'sent' ? 'message sent' : 'message received';
        var messageDiv = '<div class="' + messageClass + '"><div class="message-content">' + content + '</div></div>';
        $('.chatbox-messages').append(messageDiv);
        // Scroll to the bottom of the chatbox to show the latest message
        $('.chatbox-messages').scrollTop($('.chatbox-messages')[0].scrollHeight);
    }

    function createInputEle() {
        // Create an input element
        var inputElement = $('<input class="normal_text_input" type="text" placeholder="Type your response...">');
        
        // Return the input element
        return [inputElement];
    }
    

    function createSelectEle(concatenatedSelectOption){
        selectOptions = concatenatedSelectOption.split('|');
                    
        // Create a select element
        var selectElement = $('<select></select>');
    
        // Create and append option elements for each option in selectOptions
        selectOptions.forEach(function(optionValue) {
            var option = $('<option></option>').text(optionValue).attr('value', optionValue);
            selectElement.append(option);
        });

        //return the select element
        return [selectElement];
    }


    function createButtonSelectEle(concatenatedSelectOption) {
        var selectOptions = concatenatedSelectOption.split('|');
        
        // Create a hidden select element
        var selectElement = $('<select class="hidden-for-button-select"></select>');
    
        // Create and append option elements for each option in selectOptions
        selectOptions.forEach(function (optionValue) {
            var option = $('<option></option>').text(optionValue).attr('value', optionValue);
            selectElement.append(option);
        });
    
        // Create the container div for the button-like select
        var buttonSelectContainer = $('<div class="button-select-container"></div');
        var buttonSelect = $('<div class="button-select"></div');
    
        // Create divs for each option in the button-like select
        selectOptions.forEach(function (optionValue) {
            var optionDiv = $('<div class="button-select-option"></div>')
                .text(optionValue)
                .attr('value', optionValue)
                .on('click', function () {
                    selectOption(optionValue);
                });
            
            buttonSelect.append(optionDiv);
        });
    
        // Append the button-like select to the container
        buttonSelectContainer.append(buttonSelect);
    
        // Return the container div
        return [selectElement,buttonSelectContainer];
    }


    function createButtonMulSelectEle(concatenatedSelectOption) {
        var selectOptions = concatenatedSelectOption.split('|');
        
        // Create a hidden select element with the multiple attribute
        var selectElement = $('<select class="hidden-for-button-select" multiple></select');
    
        // Create and append option elements for each option in selectOptions
        selectOptions.forEach(function (optionValue) {
            var option = $('<option></option>').text(optionValue).attr('value', optionValue);
            selectElement.append(option);
        });
    
        // Create the container div for the button-like select
        var buttonSelectContainer = $('<div class="button-select-container"></div');
        var buttonSelect = $('<div class="button-select"></div');
    
        // Create divs for each option in the button-like select
        selectOptions.forEach(function (optionValue) {
            var optionDiv = $('<div class="button-select-option"></div>')
                .text(optionValue)
                .attr('value', optionValue)
                .on('click', function () {
                    toggleMultiSelectOption(optionValue);
                });
    
            buttonSelect.append(optionDiv);
        });
    
        // Append the button-like select to the container
        buttonSelectContainer.append(buttonSelect);
    
        // Return the container div
        return [selectElement, buttonSelectContainer];
    }
    
    function selectOption(value) {
        // Remove the highlight from all options
        $('.button-select-option').removeClass('selected');

        // Add the highlight to the selected option
        $('.button-select-option:contains(' + value + ')').addClass('selected');
        $('.hidden-for-button-select').attr('value',value);
    }  
    
    function toggleMultiSelectOption(value) {
        // Toggle the selected state for the clicked option
        var optionDiv = $('.button-select-option[value="' + value + '"]');
        optionDiv.toggleClass('selected');
    
        // Update the hidden select element based on selected options
        updateHiddenMultiSelect();
    }
    
    function updateHiddenMultiSelect() {
        var selectedValues = [];
        $('.button-select-option.selected').each(function() {
            selectedValues.push($(this).attr('value'));
        });
        $('.hidden-for-button-select').val(selectedValues);
    }
  

    function appendUserInputEle(elements){
        // Append the select element to the 'user-input' section
        $('#user-input').html('');
        elements.forEach(function (ele){
            $('#user-input').append(ele);
        });
    }




    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    //main
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    // Function to fetch the first question
    function fetchBotQuestion(nextQuestionId) {
        $.post(hic_ajax_url, {
            action: 'fetch_bot_question',
            nextQuestionId: nextQuestionId
        }, function (response) {
            // console.log(response);
            if (response.success) {
                currentQuestion = response.question;
                addMessage('received', currentQuestion.question_text);
                if (currentQuestion.type === 'select') {
                    appendUserInputEle(createSelectEle(currentQuestion.value));
                }else if(currentQuestion.type === 'button_select'){
                    appendUserInputEle(createButtonSelectEle(currentQuestion.value));
                }else if(currentQuestion.type === 'button_multiple_select'){
                    appendUserInputEle(createButtonMulSelectEle(currentQuestion.value));
                }else if(currentQuestion.type === 'date'){
                    appendUserInputEle(creadeDateEle(currentQuestion.value));
                }else if(currentQuestion.type === 'input'){
                    appendUserInputEle(creadeInputEle(currentQuestion.value));
                }

                fetchBotQuestion(currentQuestion.next_question_id);
            } else {
                // addMessage('received', '');
            }
        }, 'json'); // Specify the expected response type as 'json'
    }

    // // Function to send user responses to the server
    // function sendUserResponse(response) {
    //     // Send the user's response to the server
    //     $.post(hic_ajax_url, {
    //         action: 'process_user_response',
    //         user_response: response
    //     }, function (response) {
    //         if (response.success) {
    //             currentQuestion = response.question;
    //             if (currentQuestion.type === 'select') {
    //                 selectOptions = currentQuestion.options.split(',');
    //             }
    //             addMessage('received', currentQuestion.question_text);
    //             if (currentQuestion.type === 'select') {
    //                 addMessage('received', 'Options: ' + selectOptions.join(', '));
    //             }
    //         } else {
    //             addMessage('received', 'No more questions available.');
    //         }
    //     });
    // }

    // Function to handle user input and send it to the server
    function handleUserInput() {
        var userMessage = $('#user-input').children().val();
        addMessage('sent', userMessage);
        // if (currentQuestion.type === 'select') {
        //     if (selectOptions.includes(userMessage)) {
        //         sendUserResponse(userMessage);
        //     } else {
        //         addMessage('received', 'Invalid option. Please select from the given options.');
        //     }
        // } else {
        //     sendUserResponse(userMessage);
        // }
        // $('#user-input input').val('');
    }

    // Fetch the first question when the page loads
    fetchBotQuestion(nextQuestionId);
    // console.log(nextQuestionId);
    // console.log(currentQuestion);



    // Handle user input and send messages
    $('#send-button').click(function () {
        handleUserInput();
    });

    $('#user-input input').keypress(function (event) {
        if (event.keyCode === 13) {
            handleUserInput();
        }
    });
});
