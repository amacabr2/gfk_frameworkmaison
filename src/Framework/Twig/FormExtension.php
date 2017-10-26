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
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } else if(array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else{
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
     * @param array $attributes
     * @return string
     */
    private function file(array $attributes): string {
        return "<input type=\"file\" " . $this->getHTMLFromArray($attributes) . ">";
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
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes) {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHTMLFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHTMLFromArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function checkbox(?string $value, array $attributes): string {
        $html = '<input type="hidden"' . $attributes['name'] . '"value=0">';
        if ($value) {
            $attributes['checked'] = true;
        }
        return $html . "<input type=\"checkbox\" " . $this->getHTMLFromArray($attributes) . " value=\"1\">";
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
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string)$key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
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