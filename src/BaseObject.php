<?php

namespace CBSan\Tools;

/**
 * Ferramenta utilizada para manipulação e retorno de conteúdo para classes que
 * vierem a utilizar.
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
trait BaseObject
{
    /**
     * Retorna os dados contidos na classe requisitada no formato de um array.
     *
     * @return array
     */
    public function getParams()
    {
        $inputs = array_keys(get_class_vars(get_class($this)));

        return array_combine($inputs, array_map(function ($input) {
            $input = sprintf('get%s', ucfirst($input));

            return $this->$input();
        }, $inputs));
    }

    /**
     * Efetua uma limpeza no input recebido, deixando somente os números.
     *
     * @param  $input
     *
     * @return string
     */
    public function onlyNumber($input)
    {
        $input = preg_replace('/\D/i', '', $input);

        if (!is_numeric($input)) {
            return;
        }

        return $input;
    }

    /**
     * Converte para array recursivamente.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($obj) {
            if (method_exists($obj, 'toArray')) {
                return $obj->toArray();
            } else {
                return $obj;
            }
        }, $this->getParams());
    }
}
