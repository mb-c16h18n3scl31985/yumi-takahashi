<?php
//htmlspecialchars()のショートカット
function hsc($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
