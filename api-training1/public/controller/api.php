<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Api_Training
 * @subpackage Api_Training/public
 * @author     Khalid <khalinoid@gmail.com>
 */
class Api_Training_APIs {

    public function __construct()
    {
        add_shortcode('my_shortcode', [$this, 'api_training_shortcodes']);
        add_action('rest_api_init', [$this, 'register_custom_api']);
    }

    function api_training_shortcodes(){
        ob_start();

        echo '<div id="commentContainer" style="border: 1px solid #ccc; padding: 15px; width: 300px; margin: 20px auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <input type="text" id="userInput" placeholder="Enter your comment" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;"/>
            <button id="sendDataBtn" style="width: 100%; padding: 8px; background-color: #0073e6; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Comment</button>
            <div id="response" style="margin-top: 10px; font-family: Arial, sans-serif; font-size: 14px; color: #333;"></div>
          </div>
          <script>
            document.getElementById("sendDataBtn").onclick = function() {
              const userInput = document.getElementById("userInput").value;
              const responseDiv = document.getElementById("response");

              // validation
              if (!userInput.trim()) {
                responseDiv.textContent = "Please enter a comment before submitting.";
                return;
              }

              //send request
              fetch("http://localhost/wordpress/wp-json/api-training/v1/predict-data", {
                method: "POST",
                headers: {
                  "Content-Type": "application/json"
                },
                body: JSON.stringify({ text: userInput })
              })
              .then(response => response.json())
              .then(data => {
                console.log("Response-flask data):", data);
                if (data.error) {
                  responseDiv.textContent = "Error: " + data.error;
                } else {
                  const sentence = `User has provided ${data.sentiment} feedback with a confidence level of ${(data.confidence * 100).toFixed(2)}%.`;
                  responseDiv.innerHTML = sentence; 
                }
              })
              .catch(error => {
                responseDiv.textContent = "Error connecting to Flask app: " + error;
              });
            };
          </script>';

        return ob_get_clean();
    }

    // register a custom REST API endpoint to handle flask data
    public function register_custom_api() {
        register_rest_route('api-training/v1', '/predict-data', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_predict_data'],
            'permission_callback' => '__return_true',
        ]);
    }

    // callback fn
    public function handle_predict_data(WP_REST_Request $request) {

        $data = $request->get_json_params();
        return $this->send_comment_to_flask($data['text']);
    }

    
    public function send_comment_to_flask($comment_text) {
        // forward the data to flask api
        $response = wp_remote_post('http://192.168.100.129:5000/predict', [
            'body'    => json_encode(['text' => $comment_text]),
            'headers' => ['Content-Type' => 'application/json'],
        ]);
    
        if (is_wp_error($response)) {
            return new WP_REST_Response('Error connecting to Flask app', 500);
        }
        // get response back from flask
        $body = wp_remote_retrieve_body($response);
        $flask_data = json_decode($body, true);
    
        if (!isset($flask_data['sentiment']) || !isset($flask_data['confidence'])) {
            return new WP_REST_Response('Invalid data from Flask', 400);
        }
    
        /*
        // use response to create post on wordpress if necessary
        $post_data = [
          'post_title'   => 'Sentiment Analysis Result for: ' . $comment_text,
          'post_content' => 'Text: ' . $comment_text . '<br>' . 
                            'User has provided ' . ucfirst($flask_data['sentiment']) . ' feedback ' . 
                            'with a confidence level of ' . number_format($flask_data['confidence'] * 100, 2) . '%.',
          'post_status'  => 'publish',
          'post_type'    => 'post',
      ];
    
       $post_id = wp_insert_post($post_data);*/
    
       if (true) {
        return new WP_REST_Response([
            'sentiment' => $flask_data['sentiment'],
            'confidence' => $flask_data['confidence']
        ], 200);
        } else {
             return new WP_REST_Response('Failed to create post.', 500);
        }

    }

}
