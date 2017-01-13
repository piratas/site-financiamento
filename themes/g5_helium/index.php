<?php
/**
 * @package   Gantry 5 Theme
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   GNU/GPLv2 and later
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die;

/*
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 */

if (!class_exists('Timber')) {
    _e('Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>', 'g5_helium');
    return;
}

$gantry = Gantry\Framework\Gantry::instance();
$theme  = $gantry['theme'];

global $paged;

if (!isset($paged) || !$paged) {
    $paged = 1;
}

// We need to render contents of <head> before plugin content gets added.
$context              = Timber::get_context();
$context['page_head'] = $theme->render('partials/page_head.html.twig', $context);

// Category variables for query manipulation
$cat         = '';
$cat_include = $gantry['config']->get('content.blog.query.categories.include');
$cat_exclude = $gantry['config']->get('content.blog.query.categories.exclude');

if ($cat_include != '') {
    $cat = str_replace(' ', ',', $cat_include);
} elseif ($cat_exclude != '') {
    $cat_exclude = explode(' ', $cat_exclude);
    $new_exclude = [];
    foreach ($cat_exclude as $exclude) {
        $new_exclude[] = '-' . $exclude;
    }
    $cat = implode(',', $new_exclude);
}

// Override the main query only when $cat variable is not empty
if ($cat != '') {
    query_posts(['cat' => $cat, 'paged' => $paged]);
}

$context['posts']      = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();

$templates = ['index.html.twig'];

if (is_home()) {
    array_unshift($templates, 'home.html.twig');
}

Timber::render($templates, $context);
