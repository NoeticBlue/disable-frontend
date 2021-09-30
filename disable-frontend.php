<?php

/**
 * Plugin Name: Disable Frontend
 * Description: Disable the frontend interface of WordPress, use only as CMS and REST API.
 * Version: 1.0
 */

add_action('init', 'nb_redirect_to_backend');

function nb_redirect_to_backend()
{
  if (
    !is_admin() &&
    !nb_is_wp_login_url() &&
    !nb_is_rest_request()
  ) {
    wp_redirect(site_url('wp-admin'));
    exit();
  }
}

if (!function_exists('nb_is_rest_request')) {
  function nb_is_rest_request()
  {
    $prefix = rest_get_url_prefix();
    if (
      defined('REST_REQUEST') && REST_REQUEST
      || isset($_GET['rest_route'])
      && strpos(trim($_GET['rest_route'], '\\/'), $prefix, 0) === 0
    ) {
      return true;
    }

    $rest_url = wp_parse_url(site_url($prefix));
    $current_url = wp_parse_url(add_query_arg(array()));

    return strpos($current_url['path'], $rest_url['path'], 0) === 0;
  }
}

function nb_is_wp_login_url()
{
  $ABSPATH_MY = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, ABSPATH);

  return ((in_array($ABSPATH_MY . 'wp-login.php', get_included_files()) || in_array($ABSPATH_MY . 'wp-register.php', get_included_files())) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF'] == '/wp-login.php');
}
