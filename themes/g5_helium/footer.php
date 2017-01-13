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
 * Third party plugins that hijack the theme will call wp_footer() to get the footer template.
 * We use this to end our output buffer (started in header.php) and render into the views/page-plugin.html.twig template.
 */

$timberContext = $GLOBALS['timberContext'];

if (!isset($timberContext)) {
    throw new \Exception('Timber context not set in footer.');
}

$timberContext['content'] = ob_get_contents();
ob_end_clean();

$templates = ['page-plugin.html.twig'];
Timber::render($templates, $timberContext);
