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
            'fixed' => false,
		), $atts
	);

    ob_start(); ?>
<!-- HTML code that will replace [tcm] shortcode ------------------------------>
<!-- executed on frontend ----------------------------------------------------->

    <!-- style of the chatbot -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Red+Hat+Display&display=swap');

        #tcm-chatbot
        {
            font-family: 'Red Hat Display', sans-serif;
            <?php
                if ($tcm_atts['fixed'])
                {
                    echo 'position: fixed;';
                    echo 'bottom: 0px;';
                    echo 'right: 5%;';
                } 
            ?>
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;

            width: <?php echo $tcm_atts['width'] ?>;
            max-height: <?php echo $tcm_atts['max-height'] ?>;
            min-height: <?php echo $tcm_atts['min-height'] ?>;

            padding: 5px;
            background-color: white;
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
            font-family: 'Red Hat Display', sans-serif;
            border-radius: 20px;
            padding: 0 2px;
        }

        #tcm-button svg
        {
            position: relative;
            top: 1.5px;
            left: 0.15px;
            width: 20px;
            height: 20px;
        }
    </style>

    <!-- the chatbot container -->
    <div id="tcm-chatbot">
        <ul id="tcm-list">
        </ul>
        <form id="tcm-form" action="/api/process/form" method="post">
            <input type="text" name="tcm-input" id="tcm-input" required>
            <button type="submit" id="tcm-button">
                <svg 
                        version="1.1"
                        id="Layer_1"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" 
                        viewBox="0 0 32 32"
                        xml:space="preserve">
                    <ellipse style="fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="12.5" cy="9.5" rx="2.5" ry="3.5"/>
                    <ellipse style="fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="19.5" cy="9.5" rx="2.5" ry="3.5"/>
                    <ellipse style="fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="7.5" cy="16.5" rx="2.5" ry="3.5"/>
                    <ellipse style="fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="24.5" cy="16.5" rx="2.5" ry="3.5"/>
                    <path 
                            style="fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;"
                            d="M19,20c-0.966-0.966-1-3-3-3s-2,2-3,3
	s-4,1.069-4,3.5c0,1.381,1.119,2.5,2.5,2.5c1.157,0,3.684-1,4.5-1s3.343,1,4.5,1c1.381,0,2.5-1.119,2.5-2.5
	C23,21.207,19.966,20.966,19,20z"/>
                </svg>
            </button>
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
            chatbotList.scrollTo(0, chatbotList.scrollHeight);

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
