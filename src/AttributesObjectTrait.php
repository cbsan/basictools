<?php

namespace CBSan\Tools;

/**
 * Ferramenta utilizada para manipulação e retorno de conteúdo para classes que
 * vierem a utilizar.
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
trait AttributesObjectTrait
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
