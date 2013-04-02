<?php
/*
	some common functions
*/
?>
<?php
    chdir(dirname(__FILE__));
    include_once('constants.php');
?>
<?php
class CommonFunctions {

	function checkEmail($email) {
		if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
            return false;
        }
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }

        return true;
	}

    function generateRandStr($length) {
        $randstr = "";
            for($i=0;$i<$length;$i++)
            {
                $randnum = mt_rand(0,61);
                if($randnum < 10)
                {
                    $randstr .= chr($randnum + 48);
                }
                else
                {
                    if($randnum < 36)
                    {
                        $randstr .= chr($randnum + 55);
                    }
                    else
                    {
                        $randstr .= chr($randnum + 61);
                    }
                }
            }
        return $randstr;
    }

    function getProfilePath($profilepic, $username) {
        if($profilepic == "" || strlen($profilepic) == 0)
            return "\"userdata/default_profile_pic.jpg\"";
        else {
            $filename = end(explode("/", $profilepic));
            $file = PROFILE_PATH.$username."/".$filename;

            return "\"".$file."\"";
        }
    }

    function strip_punctuation($text) {
        $urlbrackets    = '\[\]\(\)';
        $urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
        $urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
        $urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;
     
        $specialquotes  = '\'"\*<>';
     
        $fullstop       = '\x{002E}\x{FE52}\x{FF0E}';
        $comma          = '\x{002C}\x{FE50}\x{FF0C}';
        $arabsep        = '\x{066B}\x{066C}';
        $numseparators  = $fullstop . $comma . $arabsep;
     
        $numbersign     = '\x{0023}\x{FE5F}\x{FF03}';
        $percent        = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
        $prime          = '\x{2032}\x{2033}\x{2034}\x{2057}';
        $nummodifiers   = $numbersign . $percent . $prime;
     
        return preg_replace(
            array(
            // Remove separator, control, formatting, surrogate,
            // open/close quotes.
                '/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
            // Remove other punctuation except special cases
                '/\p{Po}(?<![' . $specialquotes .
                    $numseparators . $urlall . $nummodifiers . '])/u',
            // Remove non-URL open/close brackets, except URL brackets.
                '/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
            // Remove special quotes, dashes, connectors, number
            // separators, and URL characters followed by a space
                '/[' . $specialquotes . $numseparators . $urlspaceafter .
                    '\p{Pd}\p{Pc}]+((?= )|$)/u',
            // Remove special quotes, connectors, and URL characters
            // preceded by a space
                '/((?<= )|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
            // Remove dashes preceded by a space, but not followed by a number
                '/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
            // Remove consecutive spaces
                '/ +/',
            ),
            ' ',
            $text );
    }

    function strip_simple_words($word) {
        $words = array("a", "this", "is", "or", "and", "an", "the", "with", "at", "are", "as", "by", "be");

        if(in_array($word, $words)) {
            $word = '';
        }

        return $word;
    }

    function checkURL($url) {
        if(filter_var($url, FILTER_VALIDATE_URL))
            return true;
        else
            return false;
    }

    function keyword_extraction($text) {
        $stripped_text = html_entity_decode(strip_tags($text), ENT_NOQUOTES, "UTF-8");
        $stripped_text = strtolower($this->strip_punctuation($stripped_text));

        $keywords = explode(' ', $stripped_text);
        // now getting all the occurences of words (making values as the and counting) and removing "faltu" words
        $keyword_count = array();

        foreach ($keywords as $key => $value) {
            //removing unnecessary words
            $value = preg_replace('/&(?:[a-z\d]+|#\d+|#x[a-f\d]+);/i', '', $value); // remove html entities
            $value = preg_replace('/[[:punct:]]/i', ' ', $value);   // remove puntucations
            // check for simple words
            $value = $this->strip_simple_words($value);
            $value = trim($value);

            if($value == '' || strlen($value) == 0) {
                unset($keywords[$key]);
            } else {
                if(isset($keyword_count[$value])) {
                    $keyword_count[$value]++;
                } else {
                    $keyword_count[$value] = 1;
                }
            }
        }

        return $keyword_count;
    }
};

$commonfunctions = new CommonFunctions;
?>