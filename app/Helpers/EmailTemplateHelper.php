<?php

function email_template($key)
{
    return \App\Models\EmailTemplate::where('key', $key)->first();
}

function parse_template($template, $data = [])
{
    foreach ($data as $key => $value) {
        $template = str_replace('{{ ' . $key . ' }}', $value, $template);
    }
    return $template;
}
