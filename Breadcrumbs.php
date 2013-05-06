<?php

  Class Breadcrumbs {

    protected $crumbs = array();

    public static function make() {
      $myBC = new Breadcrumbs();
      return $myBC->generate();
    }

    public function generate() {

      // Get Base URL
      $url = Request::root();

      // Explode the URI
      $uri_segments = Request::segments();

      if (!count($uri_segments)) {
        $this->_addBreadcrumb('Home',URL::to('/'), false);
      } else {
        $this->_addBreadcrumb('Home',URL::to('/'), true);
      }

      // Add Each Breadcrumb
      foreach($uri_segments as $index=>$uri) {
          $url .= '/' . $uri;
          $this->_addBreadcrumb(ucwords(str_replace('-',' ',$uri)), $url, ($index != (count($uri_segments) - 1)));
      }

      // Show the Breadcrumbs
      return $this->_show();
    }

    private function _addBreadcrumb($text, $url, $link = false) {
      $link = "<li>" . ($link ? "<a href='{$url}'>{$text}</a>" : "{$text}") . "</li>";
      $this->crumbs[] = $link;
    }

    private function _show() {
      $crumb_string = '';
      foreach($this->crumbs as $crumb) {
        $crumb_string .= $crumb;
      }
      return $crumb_string;
    }

  }
?>