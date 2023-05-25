<?php
/**
* Plugin Name: tcm
* Plugin URI: https://github.com/thibautdbs/Hackathon-ZenmonDrops-Enonce2-42XZD
* Description: A neuroscience chatbot.
* Version: 0.1
* Author: tdubois
* Author URI: https://github.com/thibautdbs
**/

function tcm_show($atts = array())
{
    // $wporg_atts = shortcode_atts(
	// 	array(
	// 		'width' => 'inherit',
    //         'height' => 'inherit',
	// 	), $atts, $tag
	// );

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
            
            overflow: hidden;
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
        <form id="tcm-form" action="/api/process/form" method="post">
            <input type="text" name="tcm-input" id="tcm-input" required>
            <button type="submit">SubmitAction</button>
        </form>
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

        appendBotMsg("msg2")
        appendUserMsg("msg3")
        appendBotMsg("msg4")

        document.forms['tcm-form'].addEventListener('submit', (event) => {
            event.preventDefault();
            // TODO do something here to show user that form is being submitted
            data = new FormData(event.target);
            appendUserMsg(data.get("tcm-input"));

            fetch(event.target.action, {
                method: 'POST',
                body: new URLSearchParams(data) // event.target is the form
            }).then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            }).then((body) => {
                // TODO handle body
            }).catch((error) => {
                // TODO handle error
            });
        });

    </script>
    
<!-- end of HTML code --------------------------------------------------------->
    <?php return ob_get_clean();
}

function tcm_shortcode($atts = array()) 
{
    static $has_one_instance = false;

    if ($has_one_instance)
    {
        ob_start(); ?>
        <script> 
            alert("Error: only one tcm-chatbot allowed per-page");
        </script>
        <?php return ob_get_clean();
    }
    $has_one_instance = true;
    return tcm_show($atts);
}

// register the [tcm] short code to be used in wordpress pages
add_shortcode('tcm', 'tcm_shortcode');

?>
