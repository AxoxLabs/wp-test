<?php
/* Template Name: Conference Template*/
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Isola
 */
get_header();
$format = get_page_template();

?>
<style>

.event-summary {
  position: relative;
  padding-left: 107px;
  min-height: 77px;
  padding-bottom: 1px;
  margin-bottom: 20px;
}
.event-summary__title a {
  color: #173f58;
}
.event-summary__date {
  text-decoration: none;
  display: block;
  top: 0;
  left: 0;
  position: absolute;
  padding: 14px 0 11px 0;
  color: #fff;
  border-radius: 50%;
   background-color: #154734;
  width: 80px;
  line-height: 1;
  transition: opacity 0.33s;
  text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.22);
}
.event-summary__date:hover {
  opacity: 0.75;
}
.event-summary__date--beige {
  background-color: #f4d35e;
}
.event-summary__month {
  display: block;
  font-size: 1.5rem;
  font-weight: 300;
  text-transform: uppercase;
}
.event-summary__day {
  display: block;
  font-size: 2.02rem;
  font-weight: 700;
}
ul.category-list {
    overflow:hidden;
    padding: 0;
    margin: 0;
}
ul.category-list li a {
    display: block;
    width: 100%;
    background: #f2f2f2;
    text-decoration: none;
    padding: 0.6em 0;
}
ul.category-list li a:hover, ul.category-list li a.active{
    background: #c95100;
    color: #fff;
}
ul.category-list li {
    list-style: none;
    float: left;
    width: 10%;
    text-align: center;
    margin-right: 2%;
 
}
ul.category-list li:last-child{
    margin-right: 0;
}
</style>
<header class="entry-header">


<?php
$tags_list = get_the_tag_list( '', '' );
if ( $tags_list ) :
?>

<?php endif; // End if $tags_list ?>
       
<div class="breadcrumbs">

<?php if (!is_front_page())
if(function_exists('the_breadcrumbs')) the_breadcrumbs();
?>
</div>
   
<div class="entry-title"><?php the_title( '<h1>', '</h1>' ); ?>
     
    </div>  
 
<div class="secondary-menu" id="2nd-menu" aria-label="secondary menu for desktop">
<?php
if ( has_nav_menu( 'secondary' ) ) {
   wp_nav_menu( array(
       'theme_location' => 'secondary'
   ) );
}
?>
</div>    

</header><!-- .entry-header -->
<div id="primary" class="content-area">
<div id="main" class="site-main">

<?php
// Set the selected tag
$current_month = date('m');
$current_year = date('Y');
$selected_term = isset($_GET['tag']) ? $_GET['tag'] : '';

// Get all the terms for the 'conference_tag' taxonomy
$terms = get_terms( array(
    'taxonomy' => 'conference_tag',
    'hide_empty' => false,
'orderby' => 'term_id',
    'order' => 'ASC',
) );
// Set the tax_query based on the selected tag
$tax_query = array();
if ( $selected_term ) {
    $tax_query[] = array(
        'taxonomy' => 'conference_tag',
        'field'    => 'slug',
        'terms'    => $selected_term,
    );
}

$args = array(
    'post_type' => 'conference',
    'tax_query' => $tax_query,
    'meta_query' => array(
        array(
            'key' => 'event_date',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE'
        )
    ),
    'orderby' => 'meta_value',
    'order' => 'ASC'
);

// Output the filter form
echo '<form method="get">';
echo '<ul class="category-list">';

// Output the "All" option
$selected = empty( $selected_term ) ? ' selected' : '';
echo '<li><a href="#" class="tag' . $selected . '" data-tag="">All</a></li>';

// Output the other options
foreach ( $terms as $term ) {
    $selected = ( $term->slug == $selected_term ) ? ' selected' : '';
    echo '<li><a href="#" class="tag' . $selected . '" data-tag="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</a></li>';
}

echo '</ul>';
echo '</form>';
echo '<h3><a href="/conferences"> View Past Conferences</a></h3><br>';
echo '<h3 class="center">'.$current_year .' Upcoming  Conferences</h3>';
// Query the posts
$query = new WP_Query( $args );

// Group the posts by month
$grouped_posts = array();
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $event_date = get_post_meta( get_the_ID(), 'event_date', true );
        $month = date( 'F', strtotime( $event_date ) );
        $grouped_posts[$month][] = get_the_ID();
    }
}

// Output the grouped posts
if ( ! empty( $grouped_posts ) ) {
foreach ( $grouped_posts as $month => $posts ) {
echo '<h3>' . $month . '</h3>';
$count = 0;
foreach ( $posts as $post_id ) {
$post = get_post( $post_id );
$event_date = get_post_meta( $post_id, 'event_date', true );
$formatted_date = date( 'l, F j, Y', strtotime( $event_date ) );
if ( $count % 2 === 0 ) {
echo '<div class="event-line colgrid stat-container">';
}
echo '<div class=" left50 event-cal stat-box warm-gray-0">';
echo '<h3 class="event-title">';
echo '<a href="' . get_permalink( $post_id ) . '" aria-label="' . esc_attr( get_the_title( $post_id ) ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a>';
echo '</h3>';
echo '<span class="event-date">' . $formatted_date . '</span><br>';
echo '<span class="event-time">08:30 AM - 09:30 AM</span>';
echo '<div class="event-location">';
echo '<span class="event-venue"></span>';
echo '</div>';
echo '</div>';
if ( $count % 2 !== 0 ) {
echo '</div>';
}
$count++;
}
if ( $count % 2 !== 0 ) {
echo '</div>';
}
}
} else {
// If no posts found, display a message
echo '<div class="wideblock overflow warm-gray-0">';
echo '<div class="smallblock white overflow">';
echo '<p class="center">No Upcoming Conference found</p>';
echo '</div>';
echo '</div>';
}

// Reset post data
wp_reset_postdata();
?>
 <script>
// Add click event listeners to the tags
var tags = document.querySelectorAll('.tag');
tags.forEach(function(tag) {
  tag.addEventListener('click', function(event) {
    event.preventDefault();
    var selectedTag = this.getAttribute('data-tag');
    // Call function to reload page with selected tag as query parameter
    window.location.href = window.location.origin + window.location.pathname + '?tag=' + selectedTag;
  });
});

</script>
</div>

</div>
<?php get_footer(); ?>
