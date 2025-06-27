<?php

$sessionValidity = 3600 * 24 * 365; // one year

$cfg['LoginCookieValidity'] = $sessionValidity;
ini_set('session.gc_maxlifetime', $sessionValidity);
