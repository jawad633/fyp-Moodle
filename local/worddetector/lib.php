<?php

defined('MOODLE_INTERNAL') || die();

// Register observer for forum post creation.
function local_worddetector_extend_navigation_course($navigation, $course, $context) {
    global $PAGE;
    // Check if the form data contains the word "hello".
    if ($PAGE->url->compare(new moodle_url('/mod/forum/post.php'), URL_MATCH_BASE)) {
        // Listen to form submission.
        $prunemform = data_submitted();
       
        if (isset($prunemform->message['text'])) {
            // Display the content of the message for debugging purposes
            echo '<pre>';
            print_r($prunemform->message['text']);
            echo '</pre>';
            
            // Check if the 'text' is a string
            if (is_string($prunemform->message['text'])) {
        
                // Load the HTML content into a DOMDocument
                $dom = new DOMDocument();
                // Suppress warnings due to malformed HTML (if any)
                libxml_use_internal_errors(true);
                $dom->loadHTML(mb_convert_encoding($prunemform->message['text'], 'HTML-ENTITIES', 'UTF-8'));
                libxml_clear_errors();
        
                // Extract all links
                $links = $dom->getElementsByTagName('a');
        
                if ($links->length > 0) {
                    // Display the extracted links
                    echo 'Extracted Links: <br>';
                    foreach ($links as $link) {
                        $url = $link->getAttribute('href'); // Get the href attribute of the link
                        echo htmlspecialchars($url) . '<br>'; // Output the URL safely

                        // Call the Python script to check if the URL is malicious
                        $pythonScriptPath = 'C:\Users\habib\Desktop\20i-0633_Jawad_Ass1\url_detector\url_script.py';
                        $escapedUrl = escapeshellarg($url);
                        $command = escapeshellcmd("python $pythonScriptPath $escapedUrl");

                        // Execute the Python script and capture the output
                        $output = shell_exec($command);
                        echo htmlspecialchars($output) . '<br>'; // Output the URL safely

                        // Trim and handle the output
                        if (trim($output) == '1') {
                            // The URL is detected as malicious
                            throw new moodle_exception('Error: Malicious URL detected in the announcement.');
                        }
                    }
                }


                                // Check if the word "hello" exists in the 'text'
                                if (strpos($prunemform->message['text'], 'hello') !== false) {
                                    throw new moodle_exception('Error: You cannot use the word "hello" in announcements.');
                                }
                                
            } else {
                // Handle the case where 'text' is not a string
                throw new moodle_exception('Error: Message content is not valid.');
            }
        }
        
        
        
        

    }
} 























