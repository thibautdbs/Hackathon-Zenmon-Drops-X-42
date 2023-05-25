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
            box-sizing: border-box;
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
            list-style: none;
        }
        
        .tcm-list > li span
        {
            margin: 0;
            margin-right: 5px;
            padding: 0px 10px;
            border-radius: 15px;
        }

        .tcm-list > li p
        {
            margin: 0;
            padding: 5px;
        }

        .tcm-user-msg
        {
            background-color: #C3F8FF;
        }

        .tcm-user-msg span
        {
            background-color: #ABD9FF;
        }

        .tcm-bot-msg
        {
            background-color: #FFF6BF;
        }
        
        .tcm-bot-msg span
        {
            background-color: #ffd966;
        }
    </style>

    <!-- the chatbot container -->
    <div id="chatbot" class="tcm-main">
        <ul id="chatbot-list" class="tcm-list">
        </ul>

    </div>

    <!-- javascript code behind the chat bot -->
    <script>
        const chatbot = document.getElementById("chatbot");
        const chatbotList = document.getElementById("chatbot-list");
        
        function appendBotMsg(msg)
        {            
            node = document.createElement("div");
            node.innerHTML = 
                `<li class="tcm-bot-msg">
                    <p><span>Bot</span>${msg}</p>
                </li>`;
            chatbotList.appendChild(node.firstChild);
        }
        
        function appendUserMsg(msg)
        {
            node = document.createElement("div");
            node.innerHTML = 
                `<li class="tcm-user-msg">
                    <p><span>User</span>${msg}</p>
                </li>`;
            chatbotList.appendChild(node.firstChild);
        }

        appendUserMsg("msg1")
        appendBotMsg("msg2")
        appendUserMsg("msg3")
        appendBotMsg("msg4")
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



