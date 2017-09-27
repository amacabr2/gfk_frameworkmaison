<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/09/17
 * Time: 13:43
 */

namespace Framework;


use DateTime;
use Framework\Validator\ValidationError;

class Validator {

    /**
     * @var array
     */
    private $params;

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * Validator constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        $this->params = $params;
    }

    /**
     * @param \string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addErrors($key, 'required');
            }
        }
        return $this;
    }

    /**
     * @param \string[] ...$keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) or empty($value)) {
                $this->addErrors($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return Validator
     */
    public function length(string $key, ?int $min, ?int $max = null): self {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) and !is_null($max) and ($length < $min or $length > $max)) {
            $this->addErrors($key, 'beetweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) and $length < $min) {
            $this->addErrors($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) and $length > $max) {
            $this->addErrors($key, 'maxLength', [$max]);
            return $this;
        }
        return $this;
    }

    /**
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self {
        $value = $this->getValue($key);
        $pattern = '/^([a-z0-9]+-?)+$/';
        if (!is_null($value) and !preg_match($pattern, $value)) {
            $this->addErrors($key, 'slug');
        }
        return $this;
    }

    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 or $errors['warning_count'] > 0 or $date === false) {
            $this->addErrors($key, 'datetime', [$format]);
        }
        return $this;
    }

    public function isValid(): bool {
        return empty($this->errors);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getValue(string $key) {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * @param string $key
     * @param string $rules
     * @param array $attributes
     */
    private function addErrors(string $key, string $rules, array $attributes = []) {
        $this->errors[$key] = new ValidationError($key, $rules, $attributes);
    }

}