<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

function field(string $name, string $label, string $type, array $values, array $errors, bool $required = false, array $options = [], string $attributes = ''): void
{
    $value = $values[$name] ?? '';
    $id = str_replace(['[', ']'], ['-', ''], $name);
    $error = $errors[$name] ?? '';
    $attrs = ' id="' . e($id) . '" name="' . e($name) . '"' . ($required ? ' required' : '')
        . ($error ? ' aria-invalid="true" aria-describedby="' . e($id) . '-error"' : '') . ' ' . $attributes;
    echo '<div class="field' . ($type === 'textarea' ? ' full' : '') . '"><label for="' . e($id) . '">' . e($label) . ($required ? ' <span class="required" aria-hidden="true">*</span>' : ' <span class="optional">(optional)</span>') . '</label>';
    if ($type === 'select') {
        echo '<select' . $attrs . '><option value="">Choose an option</option>';
        foreach ($options as $optionValue=>$optionLabel) {
            if (is_int($optionValue)) { $optionValue = $optionLabel; }
            echo '<option value="' . e($optionValue) . '"' . ($value === (string)$optionValue ? ' selected' : '') . '>' . e($optionLabel) . '</option>';
        }
        echo '</select>';
    } elseif ($type === 'textarea') {
        echo '<textarea' . $attrs . ' rows="4" maxlength="4000">' . e(is_string($value) ? $value : '') . '</textarea>';
    } else {
        echo '<input type="' . e($type) . '"' . $attrs . ' value="' . e(is_string($value) ? $value : '') . '">';
    }
    if ($error) { echo '<p class="field-error" id="' . e($id) . '-error">' . e($error) . '</p>'; }
    echo '</div>';
}

function extra_fields(array $service, array $values, array $errors): void
{
    foreach ($service['extra_fields'] as $definition) {
        $key = $definition['name']; $name = 'extra[' . $key . ']';
        $value = $values['extra'][$key] ?? ($definition['type'] === 'checkbox' ? [] : '');
        if (in_array($definition['type'], ['radio','checkbox'], true)) {
            $id = 'extra-' . $key;
            echo '<fieldset class="field full" id="' . e($id) . '" tabindex="-1"' . (isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="' . e($id) . '-error"' : '') . '><legend>' . e($definition['label']) . ($definition['required'] ? ' *' : ' (optional)') . '</legend><div class="choice-list">';
            foreach ($definition['options'] as $i=>$option) {
                $checked = is_array($value) ? in_array($option,$value,true) : $value === $option;
                echo '<label><input type="' . e($definition['type']) . '" name="' . e($name) . ($definition['type'] === 'checkbox' ? '[]' : '') . '" value="' . e($option) . '"' . ($checked ? ' checked' : '') . ($definition['required'] && $definition['type'] === 'radio' ? ' required' : '') . '>' . e($option) . '</label>';
            }
            echo '</div>';
            if (isset($errors[$name])) { echo '<p class="field-error" id="' . e($id) . '-error">' . e($errors[$name]) . '</p>'; }
            echo '</fieldset>';
        } else {
            $attrs = $definition['type'] === 'number' ? 'min="' . (int)($definition['min'] ?? 0) . '" max="' . (int)($definition['max'] ?? 1000000) . '" step="1"' : 'maxlength="2000"';
            field($name,$definition['label'],$definition['type'],[$name=>$value],$errors,$definition['required'],$definition['options'] ?? [],$attrs);
        }
    }
}

function errors_summary(array $errors): void
{
    if (!$errors) { return; }
    echo '<div class="error-summary" role="alert" tabindex="-1" id="form-errors"><h2>Please check your request</h2><ul>';
    foreach ($errors as $name=>$message) {
        $id = str_replace(['[', ']'], ['-', ''], $name);
        echo '<li>' . ($name !== '_form' ? '<a href="#' . e($id) . '">' . e($message) . '</a>' : e($message)) . '</li>';
    }
    echo '</ul></div>';
}

function hidden_security(): void
{
    echo csrf_field() . '<div class="honeypot" aria-hidden="true"><label>Leave this field empty<input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>';
}

function clean_common(array $post, array $limits, array &$errors): array
{
    $clean = [];
    foreach ($limits as $name=>$max) {
        try { $clean[$name] = input_text($post,$name,$max); }
        catch (InvalidArgumentException $exception) { $clean[$name] = ''; $errors[$name] = 'Please provide a valid ' . str_replace('_',' ',$name) . '.'; }
    }
    return $clean;
}

function validate_contact(array $post): array
{
    reject_unknown_fields($post, ['name','email','phone','subject','message','csrf','website_url','captcha_token','brief_goal','brief_timeline','brief_budget']);
    $errors = [];
    $values = clean_common($post, ['name'=>120,'email'=>254,'phone'=>40,'subject'=>200,'message'=>4000], $errors);
    foreach (['name'=>'Full name','message'=>'Message'] as $name=>$label) {
        if ($values[$name] === '') { $errors[$name] = $label . ' is required.'; }
    }
    if (!valid_email($values['email'])) { $errors['email'] = 'Enter a valid email address.'; }
    if ($values['phone'] !== '' && !valid_phone($values['phone'])) { $errors['phone'] = 'Enter a valid phone number.'; }
    if ($values['subject'] === '') { $values['subject'] = 'General project enquiry'; }
    return [$values, $errors];
}

function validate_demo(array $post): array
{
    $errors = [];
    $limits = ['name'=>120,'email'=>254,'phone'=>40,'company'=>160,'job_title'=>120,'service'=>80,'preferred_date'=>10,'preferred_time'=>5,'timezone'=>64,'mode'=>30,'message'=>4000];
    reject_unknown_fields($post,array_merge(array_keys($limits),['csrf','website_url','extra','captcha_token']));
    $values = clean_common($post,$limits,$errors);
    foreach (['name'=>'Full name','company'=>'Company','service'=>'Service','preferred_date'=>'Preferred date','preferred_time'=>'Preferred time','timezone'=>'Timezone','mode'=>'Meeting mode'] as $name=>$label) {
        if ($values[$name] === '') { $errors[$name] = $label . ' is required.'; }
    }
    if (!valid_email($values['email'])) { $errors['email'] = 'Enter a valid work email address.'; }
    if (!valid_phone($values['phone'])) { $errors['phone'] = 'Enter a valid phone number with 7–15 digits.'; }
    if (!in_array($values['mode'],config('booking.modes'),true)) { $errors['mode'] = 'Choose one of the available meeting modes.'; }
    if (!in_array($values['timezone'],config('booking.timezones'),true)) { $errors['timezone'] = 'Choose an available timezone.'; }
    if (!valid_booking_slot($values['preferred_date'],$values['preferred_time'],$values['timezone'])) {
        $errors['preferred_date'] = 'Choose a future available time within ' . config('booking.max_days_ahead') . ' days' . (config('booking.block_weekends') ? ', Monday to Friday.' : '.');
    }
    $service = data('demo_services')[$values['service']] ?? null;
    if (!$service) { $errors['service'] = 'Choose an available service or product.'; }
    $extra = $post['extra'] ?? [];
    if (!is_array($extra)) { throw new InvalidArgumentException('Invalid service answers. Please reload the form.'); }
    $values['extra'] = [];
    if ($service) {
        reject_unknown_fields($extra,array_column($service['extra_fields'],'name'));
        foreach ($service['extra_fields'] as $definition) {
            $name = $definition['name']; $errorKey = 'extra[' . $name . ']';
            if ($definition['type'] === 'checkbox') {
                $value = $extra[$name] ?? [];
                if (!is_array($value) || array_filter($value,static fn($v)=>!is_string($v)) || array_diff($value,$definition['options'])) {
                    $errors[$errorKey] = 'Choose valid options for ' . $definition['label'] . '.'; $value = [];
                }
                $value = array_values(array_unique($value));
            } else {
                try { $value = input_text($extra,$name,2000); }
                catch (InvalidArgumentException $exception) { $value = ''; $errors[$errorKey] = 'Check ' . $definition['label'] . '.'; }
                if ($value !== '' && in_array($definition['type'],['select','radio'],true) && !in_array($value,$definition['options'],true)) {
                    $errors[$errorKey] = 'Choose a valid option for ' . $definition['label'] . '.';
                }
                if ($value !== '' && $definition['type'] === 'number' && filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>$definition['min'] ?? 0,'max_range'=>$definition['max'] ?? 1000000]]) === false) {
                    $errors[$errorKey] = 'Enter a whole number within the displayed range for ' . $definition['label'] . '.';
                }
                if ($name === 'existing_site' && $value !== '' && (!filter_var($value,FILTER_VALIDATE_URL) || !in_array(parse_url($value,PHP_URL_SCHEME),['http','https'],true))) {
                    $errors[$errorKey] = 'Enter a complete website URL starting with https:// or http://.';
                }
            }
            if ($definition['required'] && ($value === '' || $value === [])) { $errors[$errorKey] = $definition['label'] . ' is required.'; }
            $values['extra'][$name] = $value;
        }
    }
    return [$values,$errors];
}

function preserve_safe_values(array $post): array
{
    // Bound flash storage and exclude anti-bot tokens. Validation still uses original input.
    unset($post['csrf'],$post['website_url'],$post['captcha_token']);
    $clean = [];
    foreach ($post as $key=>$value) {
        if (is_string($value)) { $clean[$key] = substr($value,0,4000); }
        elseif ($key === 'extra' && is_array($value)) {
            foreach (array_slice($value,0,30,true) as $field=>$answer) {
                if (is_string($answer)) { $clean['extra'][$field] = substr($answer,0,2000); }
                elseif (is_array($answer)) { $clean['extra'][$field] = array_values(array_slice(array_filter($answer,'is_string'),0,20)); }
            }
        }
    }
    return $clean;
}
