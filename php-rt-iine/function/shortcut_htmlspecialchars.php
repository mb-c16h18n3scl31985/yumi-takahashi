<?php

/**
 * htmlspecialcharsのショートカット
 * 
 * ENT_QUOTES,'UTF-8'含む
 */
function h(string $value = NULL)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
