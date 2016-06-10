<?php

/**
 * @file
 * Preprocessors and helper functions to make theming easier.
 */

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Preprocess to the template timeline-vertical.html.twig
 * @param $variables
 */
function template_preprocess_timeline_vertical(&$variables) {

    $view = $variables['view'];
    $rows = $variables['rows'];

    $variables['default_row_class'] = !empty($options['default_row_class']);

    foreach ($rows as $id => $row) {
        $variables['rows'][$id] = array();
        $variables['rows'][$id] = array(
            '#theme' => array('views_view_fields'),
            '#view' => $view,
            '#options' => $variables['options'],
            '#field' => $view->field,
            '#row' => $row,
            '#attached' => array(
                'library' => array('views_multi_timeline/views-multi-timelinejs', 'views_multi_timeline/views-multi-timelinecss'),
            ),
        );

    }
    template_preprocess_views_view_unformatted($variables);
}

/**
 * Preprocess to the template timeline-horizontal.html.twig
 * @param $variables
 */
function template_preprocess_timeline_horizontal(&$variables) {

    $view = $variables['view'];
    $rows = $variables['rows'];

    kint($rows);
    kint($variables);
    die();
}