<?php
/**
 * Helper functions.
 */

/**
 * Make the long time string short.
 * Such as 10:00am-12:00pm to 10am-12pm.
 *
 * @param  string $time Timestamp from array.
 * @return string $time
 */
function cvgt_short_clean_time( $time ) {
    // Strip the spaces in multi locations.
    $time = preg_replace('/\s+/', '', $time );
    // Remove :00 not needed.
    $time = preg_replace('/:00/', '', $time );

    return $time;
}

/**
 * Join the words of the slug.
 * Remove the slashes.
 * Gets parents-next to become parentsnext.
 *
 * @param string $slug Slug string from post.
 * @return void
 */
function cvgt_words_joined( $slug ) {
                    
    $first_letters_joined = str_replace( '-', ' ', $slug );
    $first_letters_joined = strtolower( $first_letters_joined );
    $first_letters_joined = str_replace( ' ', '', $first_letters_joined );

    return $first_letters_joined;
}

/**
 * Get an acronym from the slug.
 *
 * @param string $slug Slug string from post.
 * @return void
 */
function cvgt_first_letters_joined( $slug ) {

    $slug = str_replace( '-', ' ', $slug );
    $words = explode(" ", strtolower( $slug ) );
    $acronym = "";

    if ( count($words) <= 2 ) {
        return 'false';
    }

    foreach ($words as $w) {
        $acronym .= $w[0];
    }

    return $acronym;
}

/**
 * Get full title with space.
 *
 * @param string $slug Slug string from post.
 * @return void
 */
function cvgt_whole_title( $slug ) {

    $slug = str_replace( '-', ' ', $slug );
    $words = strtolower( $slug );

    return $words;
}