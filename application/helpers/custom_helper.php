<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('pre')) {
    function pre($content)
    {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }
}