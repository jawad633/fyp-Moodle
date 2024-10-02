<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook into the search process.
 * This function will be called whenever a search query is executed.
 */
function local_searchblocker_pre_search($query) {
    // Check if the search query contains the word "hello".
    if (strpos($query, 'hello') !== false) {
        // Show an error message to the user.
        print_error('searchblocker_error', 'local_sqlsearchblocker');
    }
}

/**
 * Extend the global search functionality.
 */
function local_searchblocker_extend_navigation($nav) {
    global $PAGE;

    // Only execute on the search page (search/index.php).
    if ($PAGE->pagetype === 'search-index') {
        // Get the search query parameter.
        $query = optional_param('q', '', PARAM_TEXT);
        if (!empty($query)) {
            // Check the query for "hello" and handle the error if necessary.
            local_searchblocker_pre_search($query);
        }
    }
}
