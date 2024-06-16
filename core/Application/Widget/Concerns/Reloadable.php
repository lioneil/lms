<?php

namespace Core\Application\Widget\Concerns;

trait Reloadable
{
    /**
     * Wrap the widget in a div with
     * the scripts to autoreload content.
     *
     * @param  string $widget
     * @return string
     */
    protected function wrap($widget)
    {
        return $this->hasIntervals()
             ? '<div id="'.$this->getContainerId().'">'.$widget.$this->getAutoReloadingScript().'</div>'
             : $widget;
    }

    /**
     * Create the container ID.
     *
     * @return string
     */
    protected function getContainerId()
    {
        return 'widget-'.str_slug($this->alias);
    }

    /**
     * Retrieve the auto-reloading script.
     *
     * @return string
     */
    protected function getAutoReloadingScript()
    {
        return '<script>setTimeout(function() {'.
            $this->getAjaxScript().
        '}, '.$this->getIntervals().')</script>';
    }

    /**
     * Build the javascript code to execute
     * an ajax request every interval specified.
     *
     * @return string
     */
    protected function getAjaxScript()
    {
        $url = config('widgets.url', route('core.widgets.show'));
        $params = http_build_query(['_token' => csrf_token(), 'alias' => $this->alias]);

        return
            'setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "'.$url.'", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if(xhr.readyState == 4 && xhr.status == 200) {
                        var container = document.getElementById("'.$this->getContainerId().'");
                        container.outerHTML = xhr.responseText;
                        var scripts = container.getElementsByTagName("script");
                        for(var i=0; i < scripts.length; i++) {
                            if (window.execScript) {
                                window.execScript(scripts[i].text);
                            } else {
                                window["eval"].call(window, scripts[i].text);
                            }
                        }
                    }
                };
                xhr.send("'.$params.'");
            }, 0);';
    }
}
