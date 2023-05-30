<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Isola
 */

get_header(); ?>
<style>
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

<section id="primary" class="content-area">
<div id="main" class="site-main">

<?php if ( have_posts() ) : ?>
<header class="entry-header">
<div class="breadcrumbs">
<?php if (!is_front_page())
if(function_exists('the_breadcrumbs')) the_breadcrumbs();
?>
</div>


<div class="entry-title"><h1>

</h1></div>

        <div class="secondary-menu" id="2nd-menu" aria-label="secondary menu for desktop">
          <?php
          if ( has_nav_menu( 'secondary' ) ) {
              wp_nav_menu( array(
                  'theme_location' => 'secondary'
              ) );
          }
          ?>
   </div>          

</header><!-- .page-header -->
<?php
$selected_term = isset($_GET['tag']) ? $_GET['tag'] : '';
// Get all the terms for the 'conference_tag' taxonomy
$terms = get_terms( array(
    'taxonomy' => 'conference_tag',
    'hide_empty' => false,
     'orderby' => 'term_id',
    'order' => 'ASC',
) );


 
// Output the filter form
echo '<form method="get">';
echo '<ul class="category-list">';

// Output the "All" option
$selected = empty( $selected_term ) ? ' selected' : '';
echo '<li><a href="?tag" class="tag' . $selected . '" data-tag="">All</a></li>';

// Output the other options
foreach ( $terms as $term ) {
    $selected = ( $term->slug == $selected_term ) ? ' selected' : '';
    echo '<li><a href="?tag" class="tag' . $selected . '" data-tag="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</a></li>';
}

echo '</ul>';
echo '</form>';
echo '<h3><a href="/jsom-conferences"> View Upcoming Conferences!</a></h3><br>';
$current_year = date('Y');

// Set the tax_query based on the selected tag
$tax_query = array();
if ( $selected_term ) {
    $tax_query = array(
        array(
            'taxonomy' => 'conference_tag',
            'field'    => 'slug',
            'terms'    => $selected_term,
        ),
    );
}

$args = array(
    'post_type' => 'conference',
    'posts_per_page' => -1,
    'meta_key' => 'event_date',
    'meta_value' => date('Y-m-d'),
    'meta_compare' => '<=',
    'orderby' => 'meta_value',
    'order' => 'DESC',
);

?>

<div class="wideblock warm-gray-0 overflow">


<?php
// Show an optional term description.
$term_description = term_description();
if ( ! empty( $term_description ) ) :
printf( '<div class="taxonomy-description">%s</div>', $term_description );
endif;
?>
<?php


$archive_query = new WP_Query($args);
$events_by_year = array();
while ($archive_query->have_posts()) : $archive_query->the_post();
    $event_year = get_post_meta(get_the_ID(), 'event_date', true);
    $event_year = date('Y', strtotime($event_year));
    if ($event_year <= $current_year) {
        if (!isset($events_by_year[$event_year])) {
            $events_by_year[$event_year] = array();
        }
        array_push($events_by_year[$event_year], '<a href="' . get_permalink() . '">' . get_the_title() . '</a>');
    }
endwhile;
wp_reset_query();
?>



<?php if (!empty($events_by_year)) : ?>
<div class="colgrid stat-container">
<?php foreach ($events_by_year as $year => $events) : ?>
<div class="left50 stat-box white">
<ul>
<li>
<h3><?php echo $year; ?></h3>
<ul>
<?php foreach ($events as $event) : ?>
<li><?php echo $event ?></li>
<?php endforeach; ?>
</ul>
</li>
</ul>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<div class="wideblock overflow warm-gray-0">
<div class="smallblock white overflow">
<p class="center">No Past Conference found</p>
</div>
</div>
<?php endif; ?>
<?php endif; ?>
</div><!-- #main -->

</section><!-- #primary -->


 <script>
// Add click event listeners to the tags
var tags = document.querySelectorAll('.tag');
tags.forEach(function(tag) {
  tag.addEventListener('click', function(event) {
    event.preventDefault();
    var selectedTag = this.getAttribute('data-tag');
    // Call function to reload page with selected tag as query parameter
    window.location.href = window.location.origin + window.location.pathname + '?past-conference=' + selectedTag;
  });
});

</script>

<?php get_footer(); ?>