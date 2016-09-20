<?php

namespace CBSan\Tools;

/**
 * Ferramenta utilizada para formatar os inputs.
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
abstract class FormatHelper
{
    public static function onlyNumbers($input)
    {
        return preg_replace('/\D/i', '', $input);
    }
}
