<?php
/**
* Plugin Name: tcm
* Plugin URI: https://github.com/thibautdbs/Hackathon-ZenmonDrops-Enonce2-42XZD
* Description: A neuroscience chatbot.
* Version: 0.1
* Author: tdubois
* Author URI: https://github.com/thibautdbs
**/

function tcm_show()
{
    ob_start(); ?>
<!-- HTML code that will replace [tcm] shortcode ------------------------------>
<!-- executed on frontend ----------------------------------------------------->

    <!-- style of the chatbot -->
    <style>
        .tcm-main
        {
            border-radius: 10px;
            box-shadow: 2px 2px 8px -2px #464434;

        }

        .tcm-list
        {
            display: flex;
            padding:5px;
            flex-direction: column;
            gap: 5px;
        }

        .tcm-list > li
        {
            border-radius: 5px;
            border: 1px solid;
            list-style: none;
            padding: 5px;
        }

        .tcm-botMsg
        {

        }
    </style>

    <!-- the chatbot container -->
    <div id="chatbot" class="tcm-main">
        <ul id="chatbot-list" class="tcm-list">
        </ul>

    </div>

    <!-- javascript code behind the chat bot -->
    <script>
        chatbot = document.getElementById("chatbot");
        chatbotList = document.getElementById("chatbot-list");
        
        function appendMsg(msg)
        {
            element = document.createElement("li");
            element.innerHTML = msg;
            chatbotList.appendChild(element);
        }

        appendMsg("msg1")
        appendMsg("msg2")
        appendMsg("msg3")
        appendMsg("msg4")
    </script>
    
<!-- end of HTML code --------------------------------------------------------->
    <?php
    return ob_get_clean();
}

function tcm_shortcode() 
{
    return tcm_show();
}

// register the [tcm] short code to be used in wordpress pages
add_shortcode('tcm', 'tcm_shortcode');

?>



