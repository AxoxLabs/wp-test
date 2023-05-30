<?php




//Conference tests
function post_types(){
register_post_type('conference',array(
'rewrite' => array(
'slug' => 'conferences'
),
'has_archive' => true,
'public' =>true,
'labels' =>array(
  'name' =>'Conference',
'add_new_item' => 'Add New Conference',
'edit_item' => 'Edit Conference',
'all_items' => 'All Conferences',
'singular_name' => 'Conference',  
),
'supports' => array( 'title', 'editor','author', 'thumbnail', 'excerpt', 'custom-fields' ),
'menu_icon' => 'dashicons-calendar'
));
}
add_action('init','post_types');

function create_conference_taxonomy() {
    $labels = array(
        'name'              => _x( 'Conference Tags', 'taxonomy general name' ),
        'singular_name'     => _x( 'Conference Tag', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Conference Tags' ),
        'all_items'         => __( 'All Conference Tags' ),
        'parent_item'       => __( 'Parent Conference Tag' ),
        'parent_item_colon' => __( 'Parent Conference Tag:' ),
        'edit_item'         => __( 'Edit Conference Tag' ),
        'update_item'       => __( 'Update Conference Tag' ),
        'add_new_item'      => __( 'Add New Conference Tag' ),
        'new_item_name'     => __( 'New Conference Tag Name' ),
        'menu_name'         => __( 'Conference Tags' ),
    );
 
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'rewrite' => array( 'slug' => 'conference_tag' ),
    );
 
    register_taxonomy( 'conference_tag', 'conference', $args );
}

 
add_action( 'init', 'create_conference_taxonomy', 0 );
// Add the AJAX action
add_action( 'wp_ajax_nopriv_fetch_posts_by_tag', 'fetch_posts_by_tag' );
add_action( 'wp_ajax_fetch_posts_by_tag', 'fetch_posts_by_tag' );

// Function to fetch posts by tag
function fetch_posts_by_tag() {
    $tag = sanitize_text_field( $_POST['tag'] );

    $args = array(
        'post_type' => 'conference',
        'tax_query' => array(
            array(
                'taxonomy' => 'conference_tag',
                'field'    => 'slug',
                'terms'    => $tag,
            ),
        ),
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            // Display the post content here
        endwhile;
    else :
        echo 'No posts found.';
    endif;

    wp_reset_postdata();
    die(); // End AJAX request
}

?>