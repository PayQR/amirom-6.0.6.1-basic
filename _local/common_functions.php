<?php
/* ====================================================================================================================
  Application:    Amiro.CMS
  File:           Common functions
  Version:        5

  Copyright:      XXI, Amiro.CMS, All rights reserved.
=====================================================================================================================*/

// DO NOT REMOVE THIS LINE! Registering handlers {

// DO NOT REMOVE THIS LINE! } Registering handlers

include_once __DIR__ . '/eshop/pay_drivers/payqr/categorycarthook.php';


function CacheGetPageBefore(&$Cache, &$Pageuin, &$foundPageId){
  return true;
}

function CacheSavePageBefore(&$Cache, &$Pageuin, &$foundPageId){
  return true;
}



// DO NOT REMOVE THIS LINE! Declaration of handlers {

// DO NOT REMOVE THIS LINE! } Declaration of handlers

