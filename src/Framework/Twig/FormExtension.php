<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 28/09/17
 * Time: 13:59
 */

namespace Framework\Twig;


class FormExtension extends \Twig_Extension {

    /**
     * @return array
     */
    public function getFunctions(): array {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * @param array $context
     * @param string $key
     * @param string $value
     * @param string $label
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string {

        $type = $options['type'] ?? 'text';
        $class = 'form-group';
        $error = $this->getErrorsHTML($context, $key);
        $value = $this->convertValue($value);
        $attributes = [
            'id' => $key,
            'name' => $key,
            'class' => trim('form-control ' . ($options['class'] ?? ''))
        ];

        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }

        return "
             <div class=\"{$class}\">
                <label for=\"{$key}\">{$label}</label>
                {$input}
                {$error}
            </div>
        ";

    }

    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string {
        return "<input type=\"text\" " . $this->getHTMLFromArray($attributes) . " value=\"{$value}\">";
    }

    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string {
        return "<textarea " . $this->getHTMLFromArray($attributes) . " rows=\"10\">{$value}</textarea>";
    }

    /**
     * @param $context
     * @param string $key
     * @return string
     */
    private function getErrorsHTML($context, string $key) {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function getHTMLFromArray(array $attributes) {
        return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }

    /**
     * @param $value
     * @return string
     */
    private function convertValue($value): string {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }

}