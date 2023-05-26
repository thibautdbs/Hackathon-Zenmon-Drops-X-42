<?php

define('MODEL', "gpt-3.5-turbo");
define('API_KEY', '');

define('INVALID_QUESTION_ERROR', 'je suis une IA specialisee uniquement en neurosciences, je ne comprends pas la question');
define('QUESTION_VALIDATION_CONTEXT', 'answer with 1 if the question is about neuroscience, and answer with 0 otherwise');
define('CONVERSATION_CONTEXT', 'Tu es Gary. Tu es un expert en neuroscience, essayes de repondre en deux phrases avec un ton pedagogue.');

function tcm_api_request($conversation): string
{
    $ch = curl_init( "https://api.openai.com/v1/chat/completions" );
    $response_text = '';
    
    curl_setopt_array( $ch, [
        CURLOPT_HTTPHEADER => [
            "Content-type: application/json",
            "Authorization: Bearer " . API_KEY
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode( [
            "model" => MODEL,
            "messages" => $conversation,
            "stream" => false,
        ] ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_WRITEFUNCTION => function( $ch, $data ) use ( &$response_text) {
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
            $response_text = $json->choices[0]->message->content;
        }
    ] );
    curl_exec( $ch );
    curl_close( $ch );
    return $response_text;
}

function tcm_ask($message = ""): string    
{
    static $conversation = NULL;

    if ($conversation == NULL) // Initialize msgs list
        $conversation = array(["role" => "system", "content" => CONVERSATION_CONTEXT]);

        
    $res = tcm_api_request([
        ['role' => 'system', 'content' => QUESTION_VALIDATION_CONTEXT],
        ['role' => 'user', 'content' => $message]
    ]);
        
    if ($res == '0')
        return INVALID_QUESTION_ERROR;

    array_push($conversation, ["role" => 'user', "content" => $message]);
        
    $final_res = tcm_api_request($conversation);

    array_push($conversation, ["role" => 'assistant', "content" => $final_res]);

    return $final_res;
}

echo tcm_ask(htmlspecialchars($_POST["tcm-input"]));

?>