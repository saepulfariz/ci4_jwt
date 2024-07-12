<?php


function logged($key)
{
    return session()->get($key);
}
