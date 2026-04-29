<?php

if (! function_exists('receipt_line')) {
    function receipt_line($char = '-', $length = 40)
    {
        return str_repeat($char, $length);
    }
}

if (! function_exists('receipt_format')) {
    function receipt_format($left, $right, $width = 40)
    {
        $left = substr($left, 0, $width);

        $space = $width - mb_strlen($left) - mb_strlen($right);

        return $left.str_repeat(' ', max(0, $space)).$right;
    }
}

if (! function_exists('receipt_wrap')) {
    function receipt_wrap($text, $max = 20)
    {
        return str_split($text, $max);
    }
}
