<?php

/**
 * htmlspecialcharsのショートカット
 * 
 * ENT_QUOTES,'UTF-8'含む
 */
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
