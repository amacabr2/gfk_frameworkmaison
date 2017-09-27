<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/09/17
 * Time: 13:54
 */

namespace Framework\Validator;


class ValidationError {

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var array
     */
    private $attributes;

    private $messages = [
        'required' => "Le champ %s est requis",
        'slug' => "Le champ %s n'est pas un slug valide",
        'empty' => "Le champ %s ne peut être vide",
        'minLength' => "Le champ %s doit contenir plus de %d caractères",
        'beetweenLength' => "Le champ %s doit contenir entre %d et %d caractères",
        'maxLength' => "Le champ %s doit contenir moins de %d caractères",
        'datetime' => "Le champ %s doit contenir une date valide (%s)"
    ];

    /**
     * ValidationError constructor.
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = []) {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function __toString() {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }

}