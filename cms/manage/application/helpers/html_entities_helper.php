<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Convert str to UTF-8 (if not already), then convert that to HTML named entities.
// and numbered references. Compare to native htmlentities() function.
// Unlike that function, this will skip any already existing entities in the string.
// mb_convert_encoding() doesn't encode ampersands, so use makeAmpersandEntities to convert those.
// mb_convert_encoding() won't usually convert to illegal numbered entities (128-159) unless
// there's a charset discrepancy, but just in case, correct them with correctIllegalEntities.
function makeSafeEntities($str, $convertTags = 0, $encoding = "") {
  if (is_array($arrOutput = $str)) {
    foreach (array_keys($arrOutput) as $key)
      $arrOutput[$key] = makeSafeEntities($arrOutput[$key],$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeAmpersandEntities($str);
    if ($convertTags)
      $str = makeTagEntities($str);
    $str = correctIllegalEntities($str);
    return $str;
    }
}


// Convert str to UTF-8 (if not already), then convert to HTML numbered decimal entities.
// If selected, it first converts any illegal chars to safe named (and numbered) entities
// as in makeSafeEntities(). Unlike mb_convert_encoding(), mb_encode_numericentity() will
// NOT skip any already existing entities in the string, so use a regex to skip them.
function makeAllEntities($str, $useNamedEntities = 0, $encoding = "") {

  if (is_array($str)) {
    foreach ($str as $s)
      $arrOutput[] = makeAllEntities($s,$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    if ($useNamedEntities)
      $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeTagEntities($str,$useNamedEntities);
    // Fix backslashes so they don't screw up following mb_ereg_replace
    // Single quotes are fixed by makeTagEntities() above
    $str = mb_ereg_replace('\\\\',"&#92;", $str);
    mb_regex_encoding("UTF-8");
    $str = mb_ereg_replace("(?>(&(?:[a-z]{0,4}\w{2,3};|#\d{2,5};)))|(\S+?)",
                          "'\\1'.mb_encode_numericentity('\\2',array(0x0,0x2FFFF,0,0xFFFF),'UTF-8')", $str, "ime");
    $str = correctIllegalEntities($str);
    return $str;
    }
}


// Convert common characters to named or numbered entities
function makeTagEntities($str, $useNamedEntities = 1) {
	// Note that we should use &apos; for the single quote, but IE doesn't like it
	$arrReplace = $useNamedEntities ? array('&#39;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;');
	return str_replace(array("'",'"','<','>'), $arrReplace, $str);
}


// Convert ampersands to named or numbered entities.
// Use regex to skip any that might be part of existing entities.
function makeAmpersandEntities($str, $useNamedEntities = 1) {
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $useNamedEntities ? "&amp;" : "&#38;", $str);
}

// Convert illegal HTML numbered entities in the range 128 - 159 to legal couterparts
function correctIllegalEntities($str) {

  $chars = array(
    128 => '&#8364;',
    130 => '&#8218;',
    131 => '&#402;',
    132 => '&#8222;',
    133 => '&#8230;',
    134 => '&#8224;',
    135 => '&#8225;',
    136 => '&#710;',
    137 => '&#8240;',
    138 => '&#352;',
    139 => '&#8249;',
    140 => '&#338;',
    142 => '&#381;',
    145 => '&#8216;',
    146 => '&#8217;',
    147 => '&#8220;',
    148 => '&#8221;',
    149 => '&#8226;',
    150 => '&#8211;',
    151 => '&#8212;',
    152 => '&#732;',
    153 => '&#8482;',
    154 => '&#353;',
    155 => '&#8250;',
    156 => '&#339;',
    158 => '&#382;',
    159 => '&#376;');
  foreach (array_keys($chars) as $num)
    $str = str_replace("&#".$num.";", $chars[$num], $str);
  return $str;
}

// Compare to native utf8_encode function, which will re-encode text that is already UTF-8
function makeUTF8($str,$encoding = "") {

  if ($str !== "") {
//    if (empty($encoding) && isUTF8($str)) $encoding = "UTF-8";

    if (empty($encoding)) $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');

    if (empty($encoding))
      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
    }
  }

// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {

	/* LDW: This is erroring on long strings, so i commented out the call to it in make UTF8 */

   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);

  }	
  