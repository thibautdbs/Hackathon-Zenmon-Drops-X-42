<?php
/**
* Plugin Name: tcm
* Plugin URI: https://github.com/thibautdbs/Hackathon-ZenmonDrops-Enonce2-42XZD
* Description: A neuroscience chatbot.
* Version: 0.1
* Author: tdubois
* Author URI: https://github.com/thibautdbs
**/

function send_chatgpt_message(
    // array $messages = ["coucou chat gpt"],
    array $messages = [["role" => "system", "content" => "Ne reponds plus que par OUI"]],
    string $api_key = "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",
    string $model = "gpt-3.5-turbo"
): string {
    $ch = curl_init( "https://api.openai.com/v1/chat/completions" );

    $response_text = "";
    
    curl_setopt_array( $ch, [
        CURLOPT_HTTPHEADER => [
            "Content-type: application/json",
            "Authorization: Bearer $api_key"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode( [
            "model" => $model,
            "messages" => $messages,
            "stream" => true,
        ] ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_WRITEFUNCTION => function( $ch, $data ) use ( &$response_text ) {
            $json = json_decode( $data );

            if( isset( $json->error ) ) {
                $error  = $json->error->message;
                $error .= " (" . $json->error->code . ")";
                $error  = "`" . trim( $error ) . "`";

                echo "data: " . json_encode( ["content" => $error] ) . "\n\n";

                echo "event: stop\n";
                echo "data: stopped\n\n";

                flush();
                die();
            }

            $deltas = explode( "\n", $data );
        
            foreach( $deltas as $delta ) {
                if( strpos( $delta, "data: " ) !== 0 ) {
                    continue;
                }
        
                $json = json_decode( substr( $delta, 6 ) );
        
                if( isset( $json->choices[0]->delta ) ) {
                    $content = $json->choices[0]->delta->content ?? "";
                } elseif( trim( $delta ) == "data: [DONE]" ) {
                    $content = "";
                } else {
                    error_log( "Invalid ChatGPT response: " . $delta );
                }
                // echo "RESULTAT : ";
                // echo $content;
                $response_text .= $content;
        
                // echo "data: " . json_encode( ["content" => $content] ) . "\n\n";
                flush();
            }
        
            if( connection_aborted() ) return 0;
                return strlen( $data );
        }
    ] );
    
    $response = curl_exec( $ch );

    if( ! $response ) {
        echo "custom Error in OpenAI request";
        // throw new CurlErrorException( sprintf(
        //     "Error in OpenAI request: %s",
        //     curl_errno( $ch ) . ": " . curl_error( $ch )
        // ) );
    }

    if( ! $response_text ) {
        echo "Unknown in OpenAI API request";
    }

    return $response_text;
}

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
/*

/*
    $res = send_chatgpt_message();
    echo "Resulat final : ";
    print_r($res);
    $res = send_chatgpt_message2();
    print_r($res);
}

// register the [tcm] short code to be used in wordpress pages
add_shortcode('tcm', 'tcm_shortcode');

?>



