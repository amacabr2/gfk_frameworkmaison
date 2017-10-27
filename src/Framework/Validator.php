<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/09/17
 * Time: 13:43
 */

namespace Framework;


use DateTime;
use Framework\Database\Repository;
use Framework\Validator\ValidationError;
use PDO;
use Psr\Http\Message\UploadedFileInterface;

class Validator {

    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

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
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) and !preg_match($pattern, $value)) {
            $this->addErrors($key, 'slug');
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 or $errors['warning_count'] > 0 or $date === false) {
            $this->addErrors($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $repository
     * @param PDO $pdo
     * @return Validator
     */
    public function exists(string $key, string $repository, PDO $pdo): self {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $repository WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addErrors($key, "exists", [$repository]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $repository
     * @param PDO $pdo
     * @param int|null $exclude
     * @return Validator
     */
    public function unique(string $key, string $repository, PDO $pdo, ?int $exclude = null): self {
        $value = $this->getValue($key);
        $query = "SELECT id FROM $repository WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null){
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addErrors($key, "unique", [$value]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return Validator
     */
    public function uploaded(string $key): self {
        $file = $this->getValue($key);
        if ($file === null or $file->getError() !== UPLOAD_ERR_OK) {
            $this->addErrors($key, 'uploaded');
        }
        return $this;
    }

    /**
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if ($file !== null and $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) or $expectedType !== $type) {
                $this->addErrors($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }


    /**
     * @return bool
     */
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