jQuery(document).ready(function ($) {
    var currentQuestion = null; // Store the current question    
    var selectOptions = null; // Store the options for select type questions
    var nextQuestionId = 'first_question';
    var answers = {}; // Store user answers

    // Add a message to the chatbox
    function addMessage(type, content) {
        // console.log(content);
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

    function createDateEle() {
        // Create a date input element
        var dateElement = $('<input type="date" class="date_input">');
    
        // Return the date input element
        return [dateElement];
    }
    

    function createSelectEle(concatenatedSelectOption) {
        selectOptions = concatenatedSelectOption.split('|');

        // Create a select element
        var selectElement = $('<select></select>');
        selectElement.append($('<option disabled selected value=\'\'>Please Choose..</option>'));

        // Create and append option elements for each option in selectOptions
        selectOptions.forEach(function (optionValue) {
            var option = $('<option></option>').text(optionValue).attr('value', optionValue);
            selectElement.append(option);
        });

        // Return the select element
        return [selectElement];
    }    


    function createButtonSelectEle(concatenatedSelectOption) {
        var selectOptions = concatenatedSelectOption.split('|');

        // Create a hidden select element
        var selectElement = $('<select class="hidden-for-button-select"></select>');
        selectElement.append($('<option disabled selected value=\'\'>Please Choose..</option>'));

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
        return [selectElement, buttonSelectContainer];
    }

    function createYesNoEle(){
        return createButtonSelectEle('yes|no');
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

    function toggleMultiSelectOption(value) {
        // Toggle the selected state for the clicked option
        var optionDiv = $('.button-select-option[value="' + value + '"]');
        optionDiv.toggleClass('selected');

        // Update the hidden select element based on selected options
        updateHiddenMultiSelect();
    }

    function updateHiddenMultiSelect() {
        var selectedValues = [];
        $('.button-select-option.selected').each(function () {
            selectedValues.push($(this).attr('value'));
        });
        $('.hidden-for-button-select').val(selectedValues);
    }

    function selectOption(value) {
        // Remove the highlight from all options
        $('.button-select-option').removeClass('selected');

        // Add the highlight to the selected option
        $('.button-select-option:contains(' + value + ')').addClass('selected');
        $('.hidden-for-button-select').val(value);
    }

    function appendUserInputEle(elements) {
        // Append the select element to the 'user-input' section
        $('#user-input').html('');
        elements.forEach(function (ele) {
            $('#user-input').append(ele);
        });
    }



    
    // Function to fetch the first question
    function fetchBotQuestion(nextQuestionId) {
        $.post(hic_ajax_url, {
            action: 'fetch_bot_question',
            nextQuestionId: nextQuestionId
        }, function (response) {
            if (response.success) {
                currentQuestion = response.question;
                addMessage('received', currentQuestion.text);
                if (currentQuestion.type === 'select') {
                    appendUserInputEle(createSelectEle(currentQuestion.value));
                } else if (currentQuestion.type === 'button_select') {
                    appendUserInputEle(createButtonSelectEle(currentQuestion.value));
                } else if (currentQuestion.type === 'button_multiple_select') {
                    appendUserInputEle(createButtonMulSelectEle(currentQuestion.value));
                } else if (currentQuestion.type === 'text') {
                    appendUserInputEle(createInputEle());
                }else if (currentQuestion.type === 'none') {
                    // Handle 'none' type by directly moving to the next question
                    fetchBotQuestion(currentQuestion.next_question_id);
                }else if (currentQuestion.type === 'date') {
                    appendUserInputEle(createDateEle());
                }else if (currentQuestion.type === 'yes_no') {

                    appendUserInputEle(createYesNoEle());
                }
            } else {
                // Handle the end of the conversation
                handleEndOfConversation();
                // addMessage('recieved','End of the conversation.')
                // console.log(answers);
            }
        }, 'json');
    }

    function handleEndOfConversation() {
        // Handle the end of the conversation

        // Display a summary of answers
        displayReloadButton();
        // Add any necessary UI updates or messages
        console.log(answers); // You can log the answers for debugging
    
        // Send answers to the backend using Ajax
        $.post(hic_ajax_url, {
            action: 'save_answers',
            answers: answers,
        }, function (response) {
            console.log(response);
            if (response.success) {
                // Optionally, handle a successful response from the server
            } else {
                // Handle any errors that may occur during the save process
            }
        }, 'json');
    }

    // Function to display a summary of answers
    function displayReloadButton() {
        $('#user-input').empty(); // Clear the user input
        $('.chatbox-messages').empty(); // Clear the chatbox area


        // Append the summary of answers to the chatbox
       // Create a summary of answers
        var buttonContainer = $('<div class="d-flex justify-content-center align-items-center w-100 h-100"></div>');
        buttonContainer.append($('.chatbot-page-url').removeClass('d-none'));
        // Iterate over answers and append them to the summary
        // for (var key in answers) {
        // var answer = answers[key];
        // var answerSummary = key + ': ' + answer;
        // var answerDiv = $('<div class="card-text">').text(answerSummary);
        // summaryDiv.find('.card-body').append(answerDiv);
        // }

        // Append the summary div to the chatbox
        $('.chatbox-messages').append(buttonContainer);


        // Scroll to the bottom of the chatbox to show the summary
        $('.chatbox-messages').scrollTop($('.chatbox-messages')[0].scrollHeight);
    }

    function storeAnswer(response){
        // console.log(currentQuestion);
        if (currentQuestion.slug !== '') {
            answers[currentQuestion.slug] = response; // Store the answer
        }
    }

    function handleUserResponse(response) {
        if (currentQuestion.type === 'select') {
            if (selectOptions.includes(response)) {
                addMessage('sent', response);
                storeAnswer(response);
                fetchBotQuestion(currentQuestion.next_question_id);
            } else {
                addMessage('received', 'Invalid option. Please select from the given options.');
            }
        } else if (currentQuestion.type === 'button_select') {
            if (response != '') {
                addMessage('sent', response);
                storeAnswer(response);
                selectOption(response);
                fetchBotQuestion(currentQuestion.next_question_id);
            } else {
                addMessage('received', 'Invalid option. Please select from the given options.');
            }
        } else if (currentQuestion.type === 'button_multiple_select') {
            if (response != '') {
                addMessage('sent', response);
                storeAnswer(response);
                toggleMultiSelectOption(response);
                fetchBotQuestion(currentQuestion.next_question_id);
            } else {
                addMessage('received', 'Invalid option. Please select from the given options.');
            }
        } else if (currentQuestion.type === 'text') {
            if(response != ''){
                addMessage('sent', response);
                storeAnswer(response);
                fetchBotQuestion(currentQuestion.next_question_id);
            }else{
                addMessage('received','Please write something');
            }
        } else if (currentQuestion.type === 'date') {;
            if(response != ''){
                addMessage('sent', response);
                storeAnswer(response);
                fetchBotQuestion(currentQuestion.next_question_id);
            }else{
                addMessage('received','Please input a date');
            }
        } else if(currentQuestion.type === 'yes_no'){
            // Handle 'Yes_no' type based on user response
            if(response != null){
                if (response.toLowerCase() === 'yes'){
                    addMessage('sent', response);
                    storeAnswer(response);
                    fetchBotQuestion(currentQuestion.value.split('|')[0]); // Use the 'Yes' branch
                }else{
                    addMessage('sent', response);
                    storeAnswer(response);
                    fetchBotQuestion(currentQuestion.value.split('|')[1]); // Use the 'No' branch
                }
            }else{
                addMessage('received', 'Invalid response. Please select "Yes" or "No".');
            }
        }
    }

    // Modify the 'handleUserInput' function to call 'handleUserResponse' when the user responds
    function handleUserInput() {
        var userMessage = $('#user-input').children().val();
        // console.log(userMessage);
        handleUserResponse(userMessage);
    }

    // Fetch the first question when the page loads
    fetchBotQuestion(nextQuestionId);

    // Handle user input and send messages
    $('#send-button').click(function () {
        handleUserInput();
    });

    $('#user-input').keypress(function (event) {
        if (event.keyCode === 13) {
            handleUserInput();
        }
    });
});
