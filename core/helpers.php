<?php

use Core\Sidebar\Sidebar;
use Illuminate\Support\Facades\Auth;

if (! function_exists('__url')) {

    /**
     * Retrieve the current url and defined parameters.
     *
     * @param  array $params
     * @return string
     */
    function __url(array $params = [])
    {
        $params = url_filter($params);

        return request()->url().(! empty($params) ? '?'.$params : null);
    }

}

if (! function_exists('core_path')) {

    /**
     * Get the path to the core folder of the install.
     *
     * @param  string $path
     * @return string
     */
    function core_path(string $path = '')
    {
        return app()->corePath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

}

if (! function_exists('modules_path')) {

    /**
     * Get the path to the modules folder of the install.
     *
     * @param  string $path
     * @return string
     */
    function modules_path(string $path = '')
    {
        return app()->modulesPath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

}

if (! function_exists('theme_path')) {
    /**
     * Get the path to the core theme folder of the install.
     *
     * @param  string  $path
     * @return string
     */
    function theme_path($path = '')
    {
        return app()->themePath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('themes_path')) {
    /**
     * Get the path to the core themes folder of the install.
     *
     * @param  string  $path
     * @return string
     */
    function themes_path($path = '')
    {
        return app()->themesPath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('stubs_path')) {
    /**
     * Get the path to the core stubs folder of the install.
     *
     * @param  string  $path
     * @return string
     */
    function stubs_path($path = '')
    {
        return app()->corePath().
            DIRECTORY_SEPARATOR.'Console'.
            DIRECTORY_SEPARATOR.'stubs'.
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('theme')) {
    /**
     * Generate a theme path for the application.
     * If no path is provided, return the
     * ThemeRepository instance.
     *
     * @param  string  $path
     * @param  bool    $secure
     * @return string
     */
    function theme($path = null, $secure = null)
    {
        if (is_null($path)) {
            return app()->make('repository:theme');
        }

        return app('url')->asset('theme/'.$path, $secure);
    }
}

if (! function_exists('get_namespace')) {
    /**
     * Generate the namespace of a given file.
     *
     * @param  string  $path
     * @return string
     */
    function get_namespace($path = '')
    {
        $ns = null;
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return $ns;
    }
}

if (! function_exists('sidebar')) {

    /**
     * Sidebar instance.
     *
     * @return string
     */
    function sidebar()
    {
        return app('sidebar')->build();
    }

}

if (! function_exists('url_filter')) {
    /**
     * Merge the url parameters.
     *
     * @param  array $params
     * @return array
     */
    function url_filter($params = [])
    {
        return http_build_query(
            array_merge(request()->all(), $params)
        );
    }
}

if (! function_exists('user')) {
    /**
     * Auth::user() instance.
     *
     * @return \Illuminate\Support\Facades\Auth
     */
    function user()
    {
        return Auth::user();
    }
}

if (! function_exists('field')) {
    /**
     * Return a Form\Field instance.
     *
     * @param string $type
     * @return Form\Field
     */
    function field($type = 'text')
    {
        return new Core\Application\Form\Field($type);
    }
}

if (! function_exists('tag')) {
    /**
     * Return a Form\Field instance.
     *
     * @param  string $type
     * @return Form\Field
     */
    function tag($type = 'text')
    {
        return new Core\Application\Form\Field($type);
    }
}

if (! function_exists('breadcrumbs')) {
    /**
     * Breadcrumbs instance for the application.
     *
     * @return \Core\Application\Breadcrumbs\Breadcrumbs
     */
    function breadcrumbs()
    {
        return app('breadcrumbs');
    }
}

if (! function_exists('widgets')) {
    /**
     * The Core widget instance.
     *
     * @param  string $alias
     * @return \Core\Application\Widget\Factories\WidgetFactory
     */
    function widgets($alias = null)
    {
        if (is_null($alias)) {
            return app('manifest:widget');
        }

        return app('core.widget')->make($alias);
    }
}

if (! function_exists('file_upload_max_size')) {
    /**
     * Retrieve the file upload max size.
     *
     * @return integer
     */
    function file_upload_max_size() {
      static $max_size = -1;

      if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = parse_size(ini_get('post_max_size'));

        if ($post_max_size > 0) {
          $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce.
        // Except if upload_max_size is zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));

        if ($upload_max > 0 && $upload_max < $max_size) {
          $max_size = $upload_max;
        }
      }

      return $max_size;
    }
}

if (! function_exists('parse_size')) {
    /**
     * Parse the given byte size.
     *
     * @return integer
     */
    function parse_size($size) {
        // Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        // Remove the non-numeric characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            // Find the position of the unit in the ordered string which is
            // the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}

if (! function_exists('bytesToHuman')) {
    /**
     * Return a human readable file size.
     *
     * @return string
     */
    function bytesToHuman($bytes) {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
