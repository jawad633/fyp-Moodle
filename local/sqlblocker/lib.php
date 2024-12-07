
<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook into the search process.
 * This function will be called whenever a search query is executed.
 */
function local_sqlblocker_pre_search($query) {
    // Check if the search query contains the word "hello".
    // if (strpos($query, 'hello') !== false) {
    //     // Show an error message to the user.
    //     print_error('sqlblocker_error', 'local_sqlblocker');
    // }

       // Path to the Python script that runs the model
      
       $pythonScriptPath = "C:/Users/habib/Desktop/20i-0633_Jawad_Ass1/sql_detector/script.py";
    
       // Escape and pass the query to the Python script
       $escapedQuery = escape_query_for_shell_with_double_quotes($query);
       $command = escapeshellcmd("python $pythonScriptPath $escapedQuery");
   
  
       // Get the output from the Python script (1 for SQL injection, 0 for normal)
    //   $output = shell_exec($command);
       $output = shell_exec($command . ' 2>&1'); // Capture stderr as well as stdout
        if ($output === null) {
            throw new moodle_exception('errorrunningpythonscript', 'local_sqlblocker', '', null, 'Python script failed to execute.');
        }

       echo "Python script output: " . htmlspecialchars($output) . "<br>";

       
       
       // Check the model's response
       if (trim($output) == 'Malicious') {
           // Throw an error message to the user if SQL injection is detected
           throw new moodle_exception('sqlblocker_error', 'local_sqlblocker');
       }
      
}

/**
 * Extend the global search functionality.
 */
function local_sqlblocker_extend_navigation($nav) {
    global $PAGE;

    // Only execute on the search page (search/index.php).
    if ($PAGE->pagetype === 'search-index') {
        // Get the search query parameter.
        $query = optional_param('q', '', PARAM_TEXT);
        if (!empty($query)) {
            // Check the query for "hello" and handle the error if necessary.
            local_sqlblocker_pre_search($query);
        }
    }
}


function escape_query_for_shell_with_double_quotes($query) {
    // Escape double quotes for the shell by prefixing with a backslash (\")
    $escapedQuery = str_replace('"', '\\"', $query);

    // Wrap the query in double quotes for the shell
    return '"' . $escapedQuery . '"';
}
