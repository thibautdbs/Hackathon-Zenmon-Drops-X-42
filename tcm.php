<?php
/**
* Plugin Name: tcm
* Plugin URI: https://github.com/thibautdbs/Hackathon-ZenmonDrops-Enonce2-42XZD
* Description: A neuroscience chatbot.
* Version: 0.1
* Author: cmassera, ffeauga, tdubois
* Author URI: https://github.com/thibautdbs
**/

/**
 * Show_chatbot
 *
 * @param  array  $atts     Shortcode attributes. Default empty.
 * @return string Shortcode output.
 */
function tcm_show($atts = array())
{
    // Normalize attribute keys, lowercase
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $tcm_atts = shortcode_atts(
		array(
			'width' => 'inherit',
            'max-height' => 'inherit',
            'min-height' => '0px',
		), $atts
	);

    ob_start(); ?>
<!-- HTML code that will replace [tcm] shortcode ------------------------------>
<!-- executed on frontend ----------------------------------------------------->

    <!-- style of the chatbot -->
    <style>
        #tcm-chatbot
        {
            display: flex;
            flex-direction: column;

            width: <?php echo $tcm_atts['width'] ?>;
            max-height: <?php echo $tcm_atts['max-height'] ?>;
            min-height: <?php echo $tcm_atts['min-height'] ?>;

            padding: 5px;
            border-radius: 10px;
            box-shadow: 2px 2px 8px -2px #464434;
        }

        #tcm-list
        {
            margin: 0;
            padding: 0;

            overflow-y: auto;
            overflow-x: hidden;
            word-wrap: break-word;

            display: flex;
            gap: 5px;
            flex-direction: column;
        }

        #tcm-list /* firefox custom scroll bar */
        {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }

        #tcm-list::-webkit-scrollbar /* webkit custom scroll bar */
        {
            width: 5px;
        }

        #tcm-list::-webkit-scrollbar-track /* webkit custom scroll bar */
        {
            background: #f1f1f1;
        }

        #tcm-list::-webkit-scrollbar-thumb /* webkit custom scroll bar */
        {
            background-color: #888;
            border-radius: 20px;
        }

        #tcm-list li:last-child /* add margin only if there's a msg */
        {
            margin-bottom: 10px;
        }

        .tcm-botmsg
        {
            list-style: none;
            padding: 5px;
            border-radius: 5px;
            background-color: #C3F8FF;
        }
        .tcm-botmsg span
        {
            margin: 0;
            margin-right: 5px;
            padding: 0px 10px;
            border-radius: 15px;
            background-color: #ABD9FF;
        }

        .tcm-usermsg
        {
            list-style: none;
            padding: 5px;
            border-radius: 5px;
            background-color: #FFF6BF;
        }
        
        .tcm-usermsg span
        {
            margin: 0;
            margin-right: 5px;
            padding: 0px 10px;
            border-radius: 15px;
            background-color: #ffd966;
        }

        #tcm-form
        {
            display: flex;
            flex-direction: row;

            border: 1px solid #ccc;
            border-radius: 20px;

            padding: 10px;
        }

        #tcm-form:focus-within
        {
            border: 1px solid #888;
        }

        #tcm-input
        {
            flex-grow: 2;
            border: none;
            outline: none;
        }

        #tcm-input:focus
        {
            outline: none;
        }

        #tcm-button
        {
            border-radius: 20px;
        }
    </style>

    <!-- the chatbot container -->
    <div id="tcm-chatbot">
        <ul id="tcm-list">
        </ul>
        <form id="tcm-form" action="/api/process/form" method="post">
            <input type="text" name="tcm-input" id="tcm-input" required>
            <button type="submit" id="tcm-button">Send</button>
        </form>
    </div>

    <!-- javascript code behind the chat bot -->
    <script>
        const chatbot = document.getElementById("tcm-chatbot");
        const chatbotList = document.getElementById("tcm-list");

        function appendBotMsg(msg)
        {            
            node = document.createElement("div");
            node.innerHTML = 
                `<li class="tcm-botmsg">
                    <span>Bot</span>
                    ${msg}
                </li>`;
            chatbotList.appendChild(node.firstChild);
        }
        
        function appendUserMsg(msg)
        {
            node = document.createElement("div");
            node.innerHTML = 
                `<li class="tcm-usermsg">
                    <span>User</span>
                    ${msg}
                </li>`;
            chatbotList.appendChild(node.firstChild);
        }

        document.forms['tcm-form'].addEventListener('submit', (event) => {
            event.preventDefault();

            // TODO do something here to show user that form is being submitted
            data = new FormData(event.target);
            appendUserMsg(data.get("tcm-input"));
            event.target.reset();

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

function tcm_shortcodes_init()
{
    add_shortcode('tcm', 'tcm_shortcode');
}

add_action('init', 'tcm_shortcodes_init');
?>
