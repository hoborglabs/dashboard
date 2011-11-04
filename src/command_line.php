<?php

function get_options(array $params) {
        $options = array();
        $shortOpt = array();
        array_walk($params, function($val, $shortParam) use(& $shortOpt) {
                $k = str_replace(':', '', $shortParam);
                $v = str_replace(':', '', $val);
                $shortOpt[$k] = $v;
         });

        $opt = getopt(implode('', array_keys($params)), $params);
        foreach ($opt as $key => $value) {
                if (isset($shortOpt[$key])) {
                        $options[$shortOpt[$key]] = $value;
                } else {
                        $options[$key] = $value;
                }
        }

        return $options;
}

