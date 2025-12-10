<?php

  /**
    This is the ReadMore plugin.

    This file contains the ReadMore plugin. It generates a
    read-more link on overview pages so that they are not
    getting too long.

    @package urlaube\readmore
    @version 0.1a1
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class ReadMore extends BaseSingleton implements Plugin {

    // HELPER FUNCTIONS

    protected static function getReadOnlyLink($content, $uri) {
      $result = $content;

      if (is_string($result)) {
        if ((ErrorHandler::class !== Handlers::getActive()) &&
            (FeedHandler::class !== Handlers::getActive()) &&
            (PageHandler::class !== Handlers::getActive())) {
          // generate form source code
          $link = tfhtml(NL.
                         "<p><a href=\"%s\"><strong>%s</strong></a></p>".NL,
                         ReadMore::class,
                         $uri,
                         "Mehr lesen &raquo;");

          $pos = stripos($result, "[readmore]");
          if (false !== $pos) {
            $result  = substr($result, 0, $pos);
            $result .= $link;
          }
        } else {
          // replace shortcode with nothing
          $result = str_ireplace("[readmore]", "", $result);
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function plugin($content) {
      $result = $content;

      if ($result instanceof Content) {
        if ($result->isset(CONTENT)) {
          $result->set(CONTENT, static::getReadOnlyLink(value($result, CONTENT), value($result, URI)));
        }
      } else {
        if (is_array($result)) {
          // iterate through all content items
          foreach ($result as $result_item) {
            if ($result_item instanceof Content) {
              if ($result_item->isset(CONTENT)) {
                $result_item->set(CONTENT, static::getReadOnlyLink(value($result_item, CONTENT), value($result_item, URI)));
              }
            }
          }
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(ReadMore::class, "plugin", FILTER_CONTENT);

  // register translation
  Translate::register(__DIR__.DS."lang".DS, ReadMore::class);
